#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
STATE_FILE="/var/tmp/ssh_line_count"
LOG_FILE="/var/log/auth.log"
EMAIL="postmaster@smarttech.sn"

# Créer le fichier d'état s'il n'existe pas
[ ! -f "$STATE_FILE" ] && wc -l < "$LOG_FILE" > "$STATE_FILE"

OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$LOG_FILE")

# Si le fichier a été vidé ou tourné
[ "$NEW_COUNT" -lt "$OLD_COUNT" ] && OLD_COUNT=0

if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    # On récupère les nouvelles lignes et on filtre SSH
    DIFF=$((NEW_COUNT - OLD_COUNT))
    tail -n "$DIFF" "$LOG_FILE" | grep -E "sshd.*Accepted|sshd.*Failed" | while read -r line; do
        {
            echo "Subject: [SSH] Activite detectee sur $(hostname)"
            echo "From: monitoring@smarttech.sn"
            echo "To: $EMAIL"
            echo ""
            echo "Alerte Securite :"
            echo "$line"
        } | /usr/sbin/sendmail -t
    done
fi

echo "$NEW_COUNT" > "$STATE_FILE"
