#!/bin/bash

# Configuration de l'environnement
export PATH=/usr/local/sbin:/usr/local/bin:/sbin:/bin:/usr/sbin:/usr/bin
export HOME=/root
export LOGNAME=root

# Configuration email
ADMIN_EMAIL="postmaster@smarttech.sn"
SERVER_NAME="mail.smarttech.sn"

# Services à surveiller
SERVICES="bind9 postfix dovecot nginx ssh cron smbd vsftpd"

# Répertoire de stockage des états
STATE_DIR="/var/tmp/services"
mkdir -p "$STATE_DIR"

# Log du script
LOG_FILE="/var/log/monitoring/services.log"
mkdir -p /var/log/monitoring

# Fonction pour envoyer un email
send_alert() {
    local service=$1
    local event=$2
    local current_state=$3
    local timestamp=$(date '+%Y-%m-%d %H:%M:%S')
    
    # Sujet du mail
    if [ "$event" = "DEMARRAGE" ]; then
        SUBJECT="[ALERTE] Service $service redémarré sur $SERVER_NAME"
        PRIORITY="High"
    else
        SUBJECT="[CRITIQUE] Service $service arrêté sur $SERVER_NAME"
        PRIORITY="Urgent"
    fi
    
    # Corps du mail
    BODY="ALERTE MONITORING - $SERVER_NAME

Événement : $event du service $service
Date/Heure : $timestamp
État actuel : $current_state
Serveur : $SERVER_NAME ($(hostname -I | awk '{print $1}'))

---
Détails du service :
$(systemctl status $service --no-pager -l 2>&1 | head -20)

---
Action recommandée :
"
    
    if [ "$event" = "ARRET" ]; then
        BODY="${BODY}Vérifier immédiatement pourquoi le service s'est arrêté.
Commande de redémarrage : systemctl restart $service"
    else
        BODY="${BODY}Le service a redémarré. Vérifier les logs pour identifier la cause.
Commande logs : journalctl -u $service -n 50"
    fi
    
    # Envoi du mail
    echo "$BODY" | mail -s "$SUBJECT" "$ADMIN_EMAIL"
    
    # Log l'envoi
    echo "[$timestamp] Email envoyé : $event $service -> $ADMIN_EMAIL" >> "$LOG_FILE"
}

# Log début d'exécution
echo "[$(date '+%Y-%m-%d %H:%M:%S')] Début vérification services" >> "$LOG_FILE"

# Vérification de chaque service
for svc in $SERVICES; do
    STATE_FILE="$STATE_DIR/$svc"
    
    # État actuel du service
    CURRENT=$(/usr/bin/systemctl is-active "$svc" 2>/dev/null)
    
    # Si échec de la commande, considérer comme inactif
    if [ -z "$CURRENT" ]; then
        CURRENT="inactive"
    fi
    
    # Lecture de l'état précédent
    if [ -f "$STATE_FILE" ]; then
        PREVIOUS=$(cat "$STATE_FILE")
    else
        PREVIOUS="unknown"
    fi
    
    # Détection de changement d'état
    if [ "$CURRENT" != "$PREVIOUS" ] && [ "$PREVIOUS" != "unknown" ]; then
        # Déterminer le type d'événement
        if [ "$CURRENT" = "active" ]; then
            EVENT="DEMARRAGE"
        else
            EVENT="ARRET"
        fi
        
        # Envoyer l'alerte
        send_alert "$svc" "$EVENT" "$CURRENT"
        
        # Log de l'événement
        echo "[$(date '+%Y-%m-%d %H:%M:%S')] $EVENT détecté : $svc ($PREVIOUS -> $CURRENT)" >> "$LOG_FILE"
    fi
    
    # Sauvegarder l'état actuel
    echo "$CURRENT" > "$STATE_FILE"
done

echo "[$(date '+%Y-%m-%d %H:%M:%S')] Fin vérification services" >> "$LOG_FILE"
