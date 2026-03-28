#!/bin/bash
LAST="/var/tmp/vnc_last"
[ ! -f "$LAST" ] && touch "$LAST"
LAST_DATE=$(stat -c %Y "$LAST")

for log in /home/*/.vnc/*.log 2>/dev/null; do
    [ ! -f "$log" ] && continue
    LOG_DATE=$(stat -c %Y "$log")
    [ "$LOG_DATE" -le "$LAST_DATE" ] && continue
    
    IP=$(grep "Got connection" "$log" | tail -1 | grep -oP '[0-9.]+' | head -1)
    
    {
        echo "Subject: [VNC] Connexion sur $(hostname)"
        echo "From: monitoring@smarttech.sn"
        echo "To: postmaster@smarttech.sn"
        echo ""
        echo "IP: ${IP:-local}"
        echo "Date: $(date)"
    } | /usr/sbin/sendmail -t
done

touch "$LAST"
