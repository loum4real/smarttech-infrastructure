#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
ADMIN_EMAIL="postmaster@smarttech.sn"
STATE_FILE="/var/tmp/crud_line_count"
LOG_FILE="/var/log/nginx/access.log"

[ ! -f "$STATE_FILE" ] && wc -l < "$LOG_FILE" > "$STATE_FILE"
OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$LOG_FILE")

if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    DIFF=$((NEW_COUNT - OLD_COUNT))
    
    # On ne filtre QUE les fichiers qui effectuent des actions en base de données
    # Remplace les noms ci-dessous par ceux de ton nouveau site (ex: add.php, save.php...)
    tail -n "$DIFF" "$LOG_FILE" | grep -Ei "insert|update|delete|save|create|edit" | while read -r line; do
        
        # On définit l'action pour le sujet du mail
        if echo "$line" | grep -iq "insert\|add\|create"; then
            ACTION="AJOUT (Create)"
        elif echo "$line" | grep -iq "update\|edit"; then
            ACTION="MODIFICATION (Update)"
        elif echo "$line" | grep -iq "delete"; then
            ACTION="SUPPRESSION (Delete)"
        else
            ACTION="Action CRUD"
        fi

        # Envoi uniquement si une action spécifique est détectée
        echo -e "Subject: [CRUD] $ACTION detectee\n\nDetail :\n$line" | /usr/sbin/sendmail "$ADMIN_EMAIL"
    done
fi

echo "$NEW_COUNT" > "$STATE_FILE"
