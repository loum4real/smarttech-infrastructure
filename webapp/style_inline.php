<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600&family=JetBrains+Mono:wght@300;400&display=swap');

:root {
  --bg:          #F7F5F2;
  --surface:     #FFFFFF;
  --border:      #E4DFD8;
  --border-dark: #C9C2B8;
  --text:        #1C1A18;
  --text-mid:    #6B6560;
  --text-soft:   #9E9890;
  --accent:      #3D6B4F;
  --accent-pale: #EDF3EF;
  --warn:        #B07D1A;
  --warn-pale:   #FDF5E4;
  --danger:      #A63025;
  --danger-pale: #FDF0EF;
  --success:     #2A7A4E;
  --success-pale:#E8F4EE;
  --mono: 'JetBrains Mono', monospace;
  --sans: 'Sora', sans-serif;
  --r: 8px;
  --shadow: 0 1px 4px rgba(28,26,24,.07), 0 0 0 1px rgba(28,26,24,.04);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
  font-family: var(--sans);
  background: var(--bg);
  color: var(--text);
  min-height: 100vh;
  font-size: 14px;
  line-height: 1.65;
  -webkit-font-smoothing: antialiased;
}

/* ─── Layout ─── */
.wrapper {
  max-width: 1080px;
  margin: 0 auto;
  padding: 44px 28px 100px;
}

/* ─── Brand ─── */
.brand {
  display: flex;
  align-items: center;
  gap: 11px;
  margin-bottom: 42px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--border);
}
.brand-icon {
  width: 36px; height: 36px;
  background: var(--text);
  border-radius: 9px;
  display: flex; align-items: center; justify-content: center;
  color: #fff;
  flex-shrink: 0;
}
.brand-name { font-size: 15px; font-weight: 600; letter-spacing: -0.3px; }
.brand-sub  { font-size: 11px; color: var(--text-soft); font-weight: 300; margin-top: 1px; }

/* ─── Page header ─── */
.page-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-bottom: 30px;
  gap: 16px;
  flex-wrap: wrap;
}
.page-header h1 {
  font-size: 22px;
  font-weight: 600;
  letter-spacing: -0.5px;
}
.page-header .meta {
  font-size: 12px;
  color: var(--text-soft);
  font-weight: 300;
  margin-top: 3px;
}

/* ─── Stats ─── */
.stats-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 12px;
  margin-bottom: 26px;
}
.stat-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: var(--r);
  padding: 16px 18px 14px;
  box-shadow: var(--shadow);
}
.stat-card .s-label {
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.1em;
  text-transform: uppercase;
  color: var(--text-soft);
  margin-bottom: 8px;
}
.stat-card .s-value {
  font-size: 30px;
  font-weight: 600;
  font-family: var(--mono);
  letter-spacing: -1px;
  line-height: 1;
}

/* ─── Buttons ─── */
.btn {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  padding: 9px 17px;
  border-radius: var(--r);
  font-family: var(--sans);
  font-size: 13px;
  font-weight: 500;
  cursor: pointer;
  text-decoration: none;
  border: 1px solid transparent;
  transition: all 0.14s;
  white-space: nowrap;
}
.btn:active { transform: scale(0.97); }

.btn-primary { background: var(--text); color: #fff; border-color: var(--text); }
.btn-primary:hover { background: #333028; }

.btn-outline { background: var(--surface); color: var(--text); border-color: var(--border); }
.btn-outline:hover { border-color: var(--border-dark); }

/* ─── Filters ─── */
.filters { display: flex; gap: 8px; margin-bottom: 18px; flex-wrap: wrap; }

.filter-btn {
  padding: 6px 13px;
  border-radius: 6px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid var(--border);
  background: transparent;
  color: var(--text-mid);
  cursor: pointer;
  text-decoration: none;
  transition: all 0.12s;
  letter-spacing: 0.01em;
}
.filter-btn:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-pale); }
.filter-btn.active { background: var(--text); color: #fff; border-color: var(--text); }

/* ─── Table card ─── */
.table-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  overflow: hidden;
  box-shadow: var(--shadow);
}

table { width: 100%; border-collapse: collapse; }

thead th {
  padding: 11px 16px;
  text-align: left;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--text-soft);
  background: var(--bg);
  border-bottom: 1px solid var(--border);
}
thead th:first-child { padding-left: 20px; }

tbody tr {
  border-bottom: 1px solid var(--border);
  transition: background 0.1s;
}
tbody tr:last-child { border-bottom: none; }
tbody tr:hover { background: #FBFAF8; }

tbody td {
  padding: 14px 16px;
  font-size: 13px;
  vertical-align: middle;
}
tbody td:first-child { padding-left: 20px; }

.row-num {
  font-family: var(--mono);
  font-size: 11px;
  color: var(--text-soft);
  font-weight: 300;
}

.company-name { font-weight: 500; margin-bottom: 2px; font-size: 13px; }
.company-addr { font-size: 11px; color: var(--text-soft); }

.contact-email { font-size: 12px; color: var(--text-mid); }
.contact-tel   { font-size: 11px; font-family: var(--mono); color: var(--text-soft); margin-top: 2px; }

/* ─── Badges ─── */
.badge {
  display: inline-block;
  padding: 2px 8px;
  border-radius: 4px;
  font-size: 11px;
  font-weight: 500;
  letter-spacing: 0.02em;
}
.badge-success  { background: var(--success-pale); color: var(--success); }
.badge-warning  { background: var(--warn-pale);    color: var(--warn);    }
.badge-neutral  { background: #F0EDE9;             color: #7A7570;        }
.badge-secteur  { background: var(--accent-pale);  color: var(--accent);  }

/* ─── Actions ─── */
.actions { display: flex; align-items: center; gap: 3px; }

.action-btn {
  width: 28px; height: 28px;
  display: inline-flex; align-items: center; justify-content: center;
  border-radius: 6px;
  border: none; cursor: pointer;
  text-decoration: none;
  background: transparent;
  color: var(--text-soft);
  transition: all 0.12s;
}
.action-btn:hover     { background: var(--accent-pale); color: var(--accent); }
.action-btn.del:hover { background: var(--danger-pale); color: var(--danger); }

/* ─── Empty ─── */
.empty-state { text-align: center; padding: 64px 24px; color: var(--text-soft); }
.empty-state .icon { font-size: 32px; opacity: 0.35; margin-bottom: 14px; }
.empty-state p { font-size: 13px; font-weight: 300; }

/* ─── Flash ─── */
.flash {
  padding: 11px 16px;
  border-radius: var(--r);
  font-size: 13px;
  margin-bottom: 22px;
  border: 1px solid transparent;
  display: flex; align-items: center; gap: 9px;
}
.flash-success { background: var(--success-pale); color: var(--success); border-color: #A8D8BE; }
.flash-error   { background: var(--danger-pale);  color: var(--danger);  border-color: #EAB5B1; }

/* ─── Form card ─── */
.form-card {
  background: var(--surface);
  border: 1px solid var(--border);
  border-radius: 10px;
  padding: 32px 36px;
  max-width: 680px;
  box-shadow: var(--shadow);
}
.form-card h2 {
  font-size: 17px;
  font-weight: 600;
  letter-spacing: -0.3px;
  margin-bottom: 26px;
  padding-bottom: 18px;
  border-bottom: 1px solid var(--border);
}

.form-group { margin-bottom: 20px; }
.form-group label {
  display: block;
  font-size: 10px;
  font-weight: 600;
  letter-spacing: 0.09em;
  text-transform: uppercase;
  color: var(--text-mid);
  margin-bottom: 6px;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 9px 12px;
  border: 1px solid var(--border);
  border-radius: var(--r);
  font-family: var(--sans);
  font-size: 13px;
  color: var(--text);
  background: var(--bg);
  outline: none;
  transition: border-color 0.14s, box-shadow 0.14s;
  appearance: none;
}
.form-group textarea { min-height: 88px; resize: vertical; line-height: 1.5; }

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  border-color: var(--accent);
  background: #fff;
  box-shadow: 0 0 0 3px rgba(61,107,79,.1);
}

.form-group input::placeholder,
.form-group textarea::placeholder { color: var(--text-soft); font-weight: 300; }

.form-group select {
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%239E9890' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 12px center;
  padding-right: 34px;
  cursor: pointer;
}

.form-row {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 14px;
}

.form-actions {
  display: flex;
  align-items: center;
  gap: 10px;
  margin-top: 28px;
  padding-top: 20px;
  border-top: 1px solid var(--border);
}

/* ─── Responsive ─── */
@media (max-width: 768px) {
  .wrapper { padding: 24px 16px 60px; }
  .form-row { grid-template-columns: 1fr; }
  .stats-grid { grid-template-columns: repeat(2, 1fr); }
  .page-header h1 { font-size: 19px; }
  table { display: block; overflow-x: auto; }
  .form-card { padding: 24px 20px; }
}
</style>
