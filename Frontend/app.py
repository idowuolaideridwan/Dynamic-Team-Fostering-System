from flask import Flask, render_template, request, redirect, url_for, session, jsonify
import requests
from routes.auth import auth_bp
from routes.dashboard import dashboard_bp
from config import API_BASE_URL
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


app = Flask(__name__)
app.secret_key = '1b6809311898523f228a08da74e74ff367cb3d65f89df408b7c7352a5c2917fe'

# Register Blueprints
app.register_blueprint(auth_bp)
app.register_blueprint(dashboard_bp)


@app.route('/')
def home():
    return redirect(url_for('auth.login'))


if __name__ == '__main__':
    app.run(port=5001, debug=True)
