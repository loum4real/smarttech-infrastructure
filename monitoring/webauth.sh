#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
STATE_FILE="/var/tmp/webauth_line_count"
# --- VERIFIE BIEN CE CHEMIN ---
LOG_FILE="/var/log/nginx/access.log" 
# ------------------------------
EMAIL="postmaster@smarttech.sn"

# Si le log n'existe pas, on arrête
[ ! -f "$LOG_FILE" ] && exit 0

# Initialisation du compteur
[ ! -f "$STATE_FILE" ] && wc -l < "$LOG_FILE" > "$STATE_FILE"

OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$LOG_FILE")

# Gestion de la rotation des logs (si le fichier est vidé)
[ "$NEW_COUNT" -lt "$OLD_COUNT" ] && OLD_COUNT=0

if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    DIFF=$((NEW_COUNT - OLD_COUNT))
    
    # On cherche les erreurs 403 (Forbidden), 401 (Unauthorized) ou 404 (Not Found)
    # pour être sûr de capter quelque chose pendant tes tests
    tail -n "$DIFF" "$LOG_FILE" | grep -E " 40[134] " | while read -r line; do
        {
            echo "Subject: [WEB] Alerte Securite sur $(hostname)"
            echo "From: monitoring@smarttech.sn"
            echo "To: $EMAIL"
            echo ""
            echo "Activite suspecte ou acces refuse detecte :"
            echo "$line"
        } | /usr/sbin/sendmail -t
    done
fi

echo "$NEW_COUNT" > "$STATE_FILE"
