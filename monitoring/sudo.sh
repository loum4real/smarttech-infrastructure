#!/bin/bash
STATE_FILE="/var/tmp/sudo_line_count"
LOG_FILE="/var/log/auth.log"
EMAIL="postmaster@smarttech.sn"

[ ! -f "$STATE_FILE" ] && wc -l < "$LOG_FILE" > "$STATE_FILE"

OLD_COUNT=$(cat "$STATE_FILE")
NEW_COUNT=$(wc -l < "$LOG_FILE")

[ "$NEW_COUNT" -lt "$OLD_COUNT" ] && OLD_COUNT=0

if [ "$NEW_COUNT" -gt "$OLD_COUNT" ]; then
    DIFF=$((NEW_COUNT - OLD_COUNT))
    tail -n "$DIFF" "$LOG_FILE" | grep "sudo:.*COMMAND=" | while read -r line; do
        {
            echo "Subject: [SUDO] Commande executee sur $(hostname)"
            echo "From: monitoring@smarttech.sn"
            echo "To: $EMAIL"
            echo ""
            echo "Detail de la commande :"
            echo "$line"
        } | /usr/sbin/sendmail -t
    done
fi

echo "$NEW_COUNT" > "$STATE_FILE"
