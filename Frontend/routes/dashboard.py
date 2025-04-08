from flask import Blueprint, render_template, session, redirect, url_for, request, jsonify
import requests
from config import API_BASE_URL
from collections import defaultdict

dashboard_bp = Blueprint('dashboard', __name__)

@dashboard_bp.route('/landingpage', methods=['GET'])
def landingpage():
    if 'token' not in session:
        return redirect(url_for('auth.login'))

    headers = {
        'Authorization': f'Bearer {session["token"]}',
        'Accept': 'application/json'
    }

    students, grades, averages = [], [], []
    page = request.args.get("page", 1)
    name_filter = request.args.get("name", "").strip()

    try:
        # Build dynamic URL with optional filters
        student_url = f"{API_BASE_URL}/students?page={page}"
        if name_filter:
            student_url += f"&search={name_filter}"

        student_res = requests.get(student_url, headers=headers, timeout=10, verify=False)
        if student_res.status_code == 200:
            students = student_res.json().get("data", {}).get("data", [])

        # Flatten students + extract modules
        module_names = set()
        flattened_students = []

        for s in students:
            grades_dict = {}
            for g in s.get("grades", []):
                module = g.get("module", {}).get("name")
                if module:
                    module_names.add(module)
                    grades_dict[module] = f"{g.get('grade', '--')}%"

            flattened_students.append({
                "Student_ID": s["student_id"],
                "First_Name": s["first_name"],
                "Last_Name": s["last_name"],
                "DOB": s["dob"],
                "Grades": grades_dict
            })

        module_list = sorted(module_names)

        # Other endpoints
        grade_res = requests.get(f"{API_BASE_URL}/students/grades", headers=headers, timeout=10, verify=False)
        if grade_res.status_code == 200:
            grades = grade_res.json().get("data", [])

        student_id_param = request.args.get("student_ids", "")
        student_ids = [s.strip() for s in student_id_param.split(",") if s.strip()]

        avg_url = f"{API_BASE_URL}/students/grades?summary_only=true"
        if student_ids:
            for sid in student_ids:
                avg_url += f"&students[]={sid}"

        # Fetch from API
        avg_res = requests.get(avg_url, headers=headers, timeout=10, verify=False)
        if avg_res.status_code == 200:
            averages = avg_res.json().get("data", [])


    except requests.exceptions.RequestException as e:
        print("API Error:", e)
        return render_template("landingpage.html",
            user_name=session.get("user_name"),
            students=[], grades=[], averages=[], module_list=[],
            error="API connection failed. Please try again later."
        )

    return render_template(
        'landingpage.html',
        user_name=session.get('user_name'),
        students=flattened_students,
        grades=grades,
        averages=averages,
        module_list=module_list
    )

@dashboard_bp.route('/landingpage/api/averages')
def get_averages_api():
    if 'token' not in session:
        return jsonify([]), 401

    headers = {
        'Authorization': f'Bearer {session["token"]}',
        'Accept': 'application/json'
    }

    student_id_param = request.args.get("student_ids", "")
    student_ids = [s.strip() for s in student_id_param.split(",") if s.strip()]

    avg_url = f"{API_BASE_URL}/students/grades?summary_only=true"
    if student_ids:
        for sid in student_ids:
            avg_url += f"&students[]={sid}"

    try:
        res = requests.get(avg_url, headers=headers, timeout=10, verify=False)
        if res.status_code == 200:
            return jsonify(res.json().get("data", []))
    except Exception as e:
        print("API error:", e)

    return jsonify([]), 500

@dashboard_bp.route('/landingpage/api/analytics', methods=['GET'])
def analytics():
    headers = {
        'Authorization': f'Bearer {session.get("token")}',
        'Accept': 'application/json'
    }

    try:
        # Fetch main data sources
        student_res = requests.get(f"{API_BASE_URL}/students", headers=headers, timeout=10, verify=False)
        summary_res = requests.get(f"{API_BASE_URL}/students/grades?summary_only=true", headers=headers, timeout=10, verify=False)

        students = student_res.json().get("data", {}).get("data", [])
        summaries = summary_res.json().get("data", [])

        # 1. Grade Classification Distribution
        gradeDist = defaultdict(int)
        for s in summaries:
            avg = s.get("average", 0)
            if avg < 40: gradeDist['Fail'] += 1
            elif avg < 60: gradeDist['Pass'] += 1
            elif avg < 70: gradeDist['Merit'] += 1
            else: gradeDist['Distinction'] += 1

        # 2. Module Averages
        module_totals = defaultdict(list)
        for s in students:
            for g in s.get("grades", []):
                module_name = g.get("module", {}).get("name")
                module_totals[module_name].append(g.get("grade", 0))

        moduleAverages = {k: round(sum(v)/len(v), 2) for k, v in module_totals.items() if v}

        # 3. Student Trends
        studentTrends = [
            {
                "student_id": s["student_id"],
                "name": f'{s["first_name"]} {s["last_name"]}',
                "modules": [
                    {"name": g.get("module", {}).get("name"), "grade": g.get("grade")}
                    for g in s.get("grades", [])
                ]
            } for s in students
        ]

        # 4. Gender Insights
        gender_map = defaultdict(lambda: defaultdict(list))
        for s in students:
            gender = s.get("profile", {}).get("gender") or "unknown"
            for g in s.get("grades", []):
                module_name = g.get("module", {}).get("name")
                grade = g.get("grade")
                gender_map[gender][module_name].append(grade)

        genderInsights = {
            g: {m: round(sum(scores)/len(scores), 2) for m, scores in modules.items() if scores}
            for g, modules in gender_map.items()
        }

        # 5. Top Performers
        topPerformers = sorted(
            [
                {"student_id": s["student_id"], "name": f'{s["first_name"]} {s["last_name"]}', "average": round(sum(g.get("grade") for g in s.get("grades", []))/len(s.get("grades", [])), 2)}
                for s in students if s.get("grades")
            ], key=lambda x: x["average"], reverse=True
        )[:5]

        # 6. Enrollment Trends
        enrollmentTrends = defaultdict(int)
        for s in students:
            year = s.get("profile", {}).get("enrollment_year")
            if year: enrollmentTrends[str(year)] += 1

        # 7. Pass Rate Per Module
        passRate = defaultdict(lambda: {"pass": 0, "fail": 0})
        for s in students:
            for g in s.get("grades", []):
                module_name = g.get("module", {}).get("name")
                if module_name:
                    if g.get("grade", 0) >= 40:
                        passRate[module_name]["pass"] += 1
                    else:
                        passRate[module_name]["fail"] += 1

        return jsonify({
            "gradeDistribution": gradeDist,
            "moduleAverages": moduleAverages,
            "studentTrends": studentTrends,
            "genderInsights": genderInsights,
            "topPerformers": topPerformers,
            "enrollmentTrends": enrollmentTrends,
            "passRateByModule": passRate
        })

    except Exception as e:
        return jsonify({"error": str(e)}), 500
