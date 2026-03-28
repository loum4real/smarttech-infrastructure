#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
ADMIN_EMAIL="postmaster@smarttech.sn"
SERVER_NAME="mail.smarttech.sn"
SFTP_LOG="/var/log/auth.log"
STATE_FILE="/var/tmp/sftp_line_count"

# 1. Initialisation du compteur (Mémoire)
[ ! -f "$STATE_FILE" ] && wc -l < "$SFTP_LOG" > "$STATE_FILE"
OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$SFTP_LOG")

# 2. On ne travaille que s'il y a du nouveau
if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    DIFF=$((NEW_COUNT - OLD_COUNT))
    
    # 3. On analyse uniquement les NOUVELLES lignes
    tail -n "$DIFF" "$SFTP_LOG" | while read -r line; do
        
        timestamp=$(date '+%Y-%m-%d %H:%M:%S')

        # Cas : Connexion réussie
        if echo "$line" | grep -q "Accepted"; then
            user=$(echo "$line" | grep -oP "for \K\w+")
            ip=$(echo "$line" | grep -oP "from \K[0-9.]+")
            
            echo -e "Subject: [INFO] Connexion SFTP reussie\n\nUtilisateur : $user\nIP : $ip\nServeur : $SERVER_NAME\nDate : $timestamp" | /usr/sbin/sendmail "$ADMIN_EMAIL"

        # Cas : Échec de connexion
        elif echo "$line" | grep -q "Failed password"; then
            user=$(echo "$line" | grep -oP "for \K\w+")
            ip=$(echo "$line" | grep -oP "from \K[0-9.]+")
            
            echo -e "Subject: [ALERTE] Echec connexion SFTP\n\nUtilisateur tente : $user\nIP : $ip\nServeur : $SERVER_NAME\nDate : $timestamp" | /usr/sbin/sendmail "$ADMIN_EMAIL"
        fi
    done
fi

# 4. On met à jour la mémoire
echo "$NEW_COUNT" > "$STATE_FILE"

# 5. Vérification du service (Optionnel, une fois par minute)
if [ "$(systemctl is-active ssh)" != "active" ]; then
    echo -e "Subject: [CRITIQUE] Service SSH DOWN\n\nLe service SSH est DOWN sur $SERVER_NAME\nDate : $(date)" | /usr/sbin/sendmail "$ADMIN_EMAIL"
fi
