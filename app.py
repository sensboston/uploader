from flask import Flask, request, jsonify
import subprocess

app = Flask(__name__)

def authenticate(username, password):
    command = f"echo {password} | su -c 'whoami' {username}"
    try:
        result = subprocess.run(command, shell=True, capture_output=True, text=True, check=True)
        return result.stdout.strip() == username
    except subprocess.CalledProcessError:
        return False

@app.route('/auth', methods=['POST'])
def auth():
    data = request.json
    username = data.get('username')
    password = data.get('password')
    if authenticate(username, password):
        return jsonify({"authenticated": True}), 200
    else:
        return jsonify({"authenticated": False}), 401

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=7000)
