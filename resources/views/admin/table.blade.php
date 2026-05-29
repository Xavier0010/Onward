@extends('layouts.admin_app')

@section('content')
<h1>Table: {{ $table }}</h1>

<!-- Index -->
<table border="1" width="100%">
    <thead id="table-head"></thead>
    <tbody id="table-body"></tbody>
</table>

<script>
    const tableName = "{{ $table }}";
    // const csrf = document.querySelector('meta[name="csrf-token"]').content;

    console.log(tableName);

    function index() {
        fetch(`/api/admin/dbms/${tableName}`)
            .then(res => res.json())
            .then(data => {
                if (!data.length) return;

                const headers = Object.keys(data[0]);

                // Table Header
                document.getElementById('table-head').innerHTML = 
                    `<tr>
                        ${headers.map(h => `<th>${h}</th>`).join('')}
                        <th>Actions</th>
                    </tr>`
                
                // Table Body
                document.getElementById('table-body').innerHTML = 
                    data.map(row => `
                        <tr>
                            ${headers.map(h => `<td>${row[h]}</td>`).join('')}
                            <td>
                                <button style='cursor: pointer;' onclick='deleteRow(${row.id})'>Delete</button>
                            </td>
                        </tr>
                    `).join('');
            })
    }
</script>