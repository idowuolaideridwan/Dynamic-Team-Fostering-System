from flask import Blueprint, render_template, session, redirect, url_for, request, jsonify
import requests
from config import API_BASE_URL

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
