#!/bin/sh

# Démarrer Xvfb
Xvfb :99 -screen 0 1024x768x16 &

# Démarrer le gestionnaire de fenêtres
fluxbox &

# Démarrer le serveur VNC
x11vnc -display :99 -nopw -forever &

# Set DISPLAY environment variable
export DISPLAY=:99
echo "Xvfb started."
echo "DISPLAY set to ${DISPLAY}."

# Install requirements
echo "Installing requirements..."
pip install -r requirements.txt
echo "Requirements installed."

# Start the main application
echo "Starting application..."

python /app/main.py
