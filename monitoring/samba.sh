
#!/bin/bash
PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
STATE_FILE="/var/tmp/samba_line_count"
LOG_FILE="/var/log/samba/log.smbd"
EMAIL="postmaster@smarttech.sn"

[ ! -f "$LOG_FILE" ] && exit 0
[ ! -f "$STATE_FILE" ] && wc -l < "$LOG_FILE" > "$STATE_FILE"

OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$LOG_FILE")
[ "$NEW_COUNT" -lt "$OLD_COUNT" ] && OLD_COUNT=0

if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    DIFF=$((NEW_COUNT - OLD_COUNT))
    tail -n "$DIFF" "$LOG_FILE" | grep "connect to service" | while read -r line; do
        {
            echo "Subject: [SAMBA] Connexion partage sur $(hostname)"
            echo "From: monitoring@smarttech.sn"
            echo "To: $EMAIL"
            echo ""
            echo "Accès au partage détecté :"
            echo "$line"
        } | /usr/sbin/sendmail -t
    done
fi
echo "$NEW_COUNT" > "$STATE_FILE"
