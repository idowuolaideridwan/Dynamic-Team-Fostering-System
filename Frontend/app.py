from flask import Flask, render_template, request, redirect, url_for, session, jsonify
import requests
from routes.auth import auth_bp
from routes.dashboard import dashboard_bp
from config import API_BASE_URL
import urllib3
urllib3.disable_warnings(urllib3.exceptions.InsecureRequestWarning)


app = Flask(__name__)

# generate new secret key
app.secret_key = ''


# Register Blueprints
app.register_blueprint(auth_bp)
app.register_blueprint(dashboard_bp)


@app.route('/')
def home():
    return redirect(url_for('auth.login'))


if __name__ == '__main__':
    app.run(port=5001, debug=True)
