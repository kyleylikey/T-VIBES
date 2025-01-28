<style>
.toursnav {
    display: flex; 
    justify-content: space-between; 
    align-items: center; 
    padding: 12px; 
    margin: 0 36px;
}

.toursnav > nav > a {
    margin: 0 8px; /* Adds some space between the links */
    border-radius: 40px; /* Makes the corners round */
    padding: 10px; /* Adds some padding for better appearance */
    text-decoration: none; /* Removes the underline from the links */
    border: 2px solid black; /* Sets the border color to blue */
}

.toursnav > nav > .active, .toursnav > nav > a:hover {
    background-color: black;
    color: white;
}

</style>


<div class="toursnav">
    <span style="margin-left:8px"><h1>Tours</h1></span>
    <nav>
        <a href="tourrequest.php" class="nav <?php echo $current_page == 'tourrequest.php' ? 'active' : ''; ?>">Plan Request</a>
        <a href="tourpending.php" class="nav <?php echo $current_page == 'tourpending.php' ? 'active' : ''; ?>">Pending</a>
        <a href="tourapproved.php" class="nav <?php echo $current_page == 'tourapproved.php' ? 'active' : ''; ?>">Approved</a>
        <a href="tourhistory.php" class="nav <?php echo $current_page == 'tourhistory.php' ? 'active' : ''; ?>">History</a>
    </nav>
</div>