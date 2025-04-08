from flask import Blueprint, render_template, request, redirect, url_for, session, jsonify
import requests
from config import API_BASE_URL

auth_bp = Blueprint('auth', __name__)

@auth_bp.route('/logout')
def logout():
    session.clear()
    return redirect(url_for('auth.login'))

@auth_bp.route('/login', methods=['GET', 'POST'])
def login():
    if 'token' in session:
        return jsonify({"message": "Already logged in", "redirect": "/landingpage"}), 200

    if request.method == 'POST':
        if request.content_type != 'application/json':
            return jsonify({"error": "Content-Type must be application/json"}), 415

        data = request.get_json(silent=True)
        if not data or not data.get("email") or not data.get("password"):
            return jsonify({"error": "Email and password are required"}), 400

        email = data["email"]
        password = data["password"]

        try:
            # Add timeout and optionally disable SSL verification for local dev
            response = requests.post(
                f"{API_BASE_URL}/login",
                json={"email": email, "password": password},
                timeout=5,
                verify=False  # 🔁 Only use this in dev if needed
            )

            if response.status_code == 200:
                api_data = response.json()
                if api_data.get("status") == "success":
                    user_data = api_data.get("data", {}).get("user", {})
                    session['token'] = user_data.get('token')
                    session['user_name'] = user_data.get('name')
                    return jsonify({"message": "Login successful", "redirect": "/landingpage"}), 200

            # Handle known error formats
            try:
                error_data = response.json()
                return jsonify({"error": error_data.get("message", "Login failed")}), response.status_code
            except ValueError:
                return jsonify({"error": "Unexpected API response"}), response.status_code

        except requests.exceptions.SSLError as ssl_err:
            return jsonify({"error": "SSL error connecting to API", "details": str(ssl_err)}), 500
        except requests.exceptions.ConnectionError as conn_err:
            return jsonify({"error": "API connection failed", "details": str(conn_err)}), 502
        except requests.exceptions.Timeout:
            return jsonify({"error": "API request timed out"}), 504
        except Exception as ex:
            return jsonify({"error": "An unexpected error occurred", "details": str(ex)}), 500

    return render_template('login.html')
