Projet SMARTTECH — Infrastructure Réseau Complète

Réalisé par : Aliou Loum  
Formation : Licence 3 GLSI  École Supérieure Polytechnique de Dakar  
Date : Février 2026

Description
Mise en place d'une infrastructure réseau complète sur Ubuntu Server 24.04 LTS.

Services déployés
| Service | Technologie | Port(s) |
|---------|-------------|---------|
| DNS | BIND9 | 53 |
| Messagerie | iRedMail (Postfix/Dovecot/Roundcube) | 25, 143, 587, 993 |
| Web Intranet | Nginx + PHP 8.3 + MariaDB | 80, 443 |
| Accès distant | SSH, RDP, VNC, noVNC | 22, 3389, 5901, 6080 |
| Partage fichiers | Samba, SFTP | 445, 22 |
| Monitoring | Scripts Bash + Cron | — |
| Pare-feu | UFW + Fail2ban | — |

Structure

configs/     → fichiers de configuration des services
monitoring/  → scripts Bash de surveillance automatisée
webapp/      → application web PHP CRUD
rapport/     → rapport technique PDF


Sécurité
- HTTPS activé (certificats SSL auto-signés)
- Fail2ban (bannissement après 3 échecs SSH)
- UFW actif
- Root SSH désactivé
