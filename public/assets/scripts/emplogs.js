document.getElementById("searchBar").addEventListener("keyup", function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll("#logTable tbody tr");
    let table = document.getElementById("logTable");
    let noLogsMsg = document.querySelector(".no-logs");

    let visibleRows = 0;
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        if (text.includes(filter)) {
            row.style.display = "";
            visibleRows++;
        } else {
            row.style.display = "none";
        }
    });

    if (visibleRows === 0) {
        if (!noLogsMsg) {
            noLogsMsg = document.createElement("div");
            noLogsMsg.className = "no-logs text-center";
            noLogsMsg.textContent = "No matching employee logs found.";
            document.querySelector(".table-responsive").appendChild(noLogsMsg);
        }
        table.style.display = "none";
    } else {
        table.style.display = "table";
        if (noLogsMsg) noLogsMsg.remove();
    }
});