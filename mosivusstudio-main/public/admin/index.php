<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: /admin/login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mosivus Admin - Dashboard</title>
    <!-- Favicon rules per PART 4 -->
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="manifest" href="/site.webmanifest">
    <meta name="theme-color" content="#0A1628">
    
    <style>
        body {
            margin: 0; padding: 24px;
            background: #050A1F; font-family: 'Inter', sans-serif; color: #F0F4FF;
            min-height: 100vh; display: flex; flex-direction: column;
        }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; flex-wrap: wrap; gap: 16px; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; font-family: sans-serif; }
        .header p { margin: 4px 0 0; color: #8B9EC4; font-size: 13px; }
        .logout-btn {
            background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.3);
            padding: 8px 16px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 600;
            transition: background 0.2s;
        }
        .logout-btn:hover { background: rgba(248,113,113,0.2); }
        .table-card {
            background: rgba(5,10,31,0.6); backdrop-filter: blur(32px) saturate(160%);
            -webkit-backdrop-filter: blur(32px) saturate(160%);
            border: 1px solid rgba(61,240,255,0.09); border-radius: 18px; padding: 28px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.5), inset 0 1px 0 rgba(255,255,255,0.04);
            overflow-x: auto; flex: 1;
        }
        table { width: 100%; border-collapse: collapse; min-width: 800px; }
        th { padding: 12px 14px; text-align: left; font-size: 10px; letter-spacing: .1em; color: #4A5980; text-transform: uppercase; border-bottom: 1px solid rgba(61,240,255,.08); }
        td { padding: 12px 14px; border-bottom: 1px solid rgba(61,240,255,.05); font-size: 13px; }
        select {
            background: rgba(10,22,40,0.75); border: 1px solid rgba(61,240,255,.12);
            border-radius: 8px; padding: 6px 10px; color: #F0F4FF; font-size: 12px; cursor: pointer; outline: none;
        }
        .status-New { color: #3DF0FF; background: rgba(61,240,255,0.1); }
        .status-Contacted { color: #60A5FA; background: rgba(96,165,250,0.1); }
        .status-InProgress { color: #EF9F27; background: rgba(239,159,39,0.1); }
        .status-Closed { color: #1D9E75; background: rgba(29,158,117,0.1); }
    </style>
</head>
<body>
    <div style="max-width: 1280px; margin: 0 auto; width: 100%;">
        <div class="header">
            <div>
                <h1>Mosivus Dashboard</h1>
                <p>Manage project submissions</p>
            </div>
            <a href="/admin/logout.php" class="logout-btn">Logout</a>
        </div>
        <div class="table-card">
            <table id="leadsTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Services</th>
                        <th>Budget</th>
                        <th>Timeline</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody><tr><td colspan="8" style="text-align: center; color: #8B9EC4;">Loading leads...</td></tr></tbody>
            </table>
        </div>
    </div>
    <script>
        async function fetchLeads() {
            try {
                const res = await fetch('/php/admin_leads.php');
                const text = await res.text();
                let data;
                try { data = JSON.parse(text); } catch(e) { window.location.href = '/admin/login.php'; return; }
                if(data.error === 'Unauthorized') { window.location.href = '/admin/login.php'; return; }

                const tbody = document.querySelector('#leadsTable tbody');
                tbody.innerHTML = '';
                if(!Array.isArray(data) || data.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="8" style="text-align: center; color: #4A5980;">No leads found.</td></tr>';
                    return;
                }
                data.forEach(l => {
                    const tr = document.createElement('tr');
                    const formattedDate = new Date(l.created_at).toLocaleDateString('en-IN', {day:'2-digit', month:'short', year:'numeric'});
                    
                    const sel = document.createElement('select');
                    ['New', 'Contacted', 'In Progress', 'Closed'].forEach(opt => {
                        const o = document.createElement('option');
                        o.value = opt; o.textContent = opt;
                        if(l.status === opt || (!l.status && opt === 'New')) o.selected = true;
                        sel.appendChild(o);
                    });
                    
                    const updateClass = () => {
                        sel.className = 'status-' + sel.value.replace(' ', '');
                    };
                    updateClass();

                    sel.onchange = async (e) => {
                        updateClass();
                        await fetch('/php/admin_update.php', {
                            method: 'POST',
                            headers: {'Content-Type': 'application/json'},
                            body: JSON.stringify({table: 'project_submissions', id: l.id, status: e.target.value})
                        });
                    };

                    tr.innerHTML = `
                        <td style="color:#4A5980; font-size:12px;">${formattedDate}</td>
                        <td style="font-weight:600;">${l.name || ''}</td>
                        <td style="color:#8B9EC4;">${l.email || ''}</td>
                        <td style="color:#8B9EC4;">${l.phone || ''}</td>
                        <td style="color:#8B9EC4; max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">${l.description || ''}</td>
                        <td style="color:#8B9EC4;">${l.budget || ''}</td>
                        <td style="color:#8B9EC4;">${l.timeline || ''}</td>
                        <td class="status-cell"></td>
                    `;
                    tr.querySelector('.status-cell').appendChild(sel);
                    tbody.appendChild(tr);
                });
            } catch (err) {
                console.error(err);
                document.querySelector('#leadsTable tbody').innerHTML = '<tr><td colspan="8" style="text-align: center; color: #f87171;">Failed to load leads.</td></tr>';
            }
        }
        fetchLeads();
    </script>
</body>
</html>
