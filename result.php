<?php
// Database connection settings
$servername = "resultdb.c9umyemakbqj.ap-south-1.rds.amazonaws.com";
$username = "admin";
$password = "admin#2003";
$dbname = "studentdb";

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the column to sort by from the URL, default is 'roll'
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'roll';

// Validate the column name to avoid SQL injection
$valid_columns = ['roll', 'ename', 'marks', 'percentage', 'grade', 'rank'];
if (!in_array($sort_column, $valid_columns)) {
    $sort_column = 'roll'; // Fallback to 'roll' if invalid column
}

// Query the table with dynamic sorting
$sql = "SELECT * FROM studentdb.marks ORDER BY `$sort_column` ASC LIMIT 1000";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Results Table</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-gradient-start: #6a82fb;
            --primary-gradient-end: #fc5c7d;
            --header-bg: rgba(106, 130, 251, 0.8);
            --header-active-bg: rgba(106, 130, 251, 0.9);
            --table-row-bg: rgba(255, 255, 255, 0.1);
            --table-row-hover: rgba(255, 255, 255, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--primary-gradient-start), var(--primary-gradient-end));
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .table-container {
            width: 100%;
            max-width: 1100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 15px 25px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
        }

        .table-title {
            text-align: center;
            padding: 20px;
            background: rgba(0,0,0,0.1);
            color: white;
            font-size: 1.5em;
            font-weight: 600;
        }

        .table-wrapper {
            max-height: 500px;
            overflow-y: auto;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        th {
            background: var(--header-bg);
            color: white;
            padding: 15px;
            text-align: center;
            text-transform: uppercase;
            font-weight: 600;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        th a {
            color: white;
            text-decoration: none;
            display: block;
            position: relative;
            z-index: 2;
        }

        th::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            transition: all 0.3s ease;
            z-index: 1;
        }

        th:hover::before {
            left: 0;
        }

        th.sort-active {
            background: var(--header-active-bg);
            box-shadow: 0 4px 6px rgba(0,0,0,0.2);
        }

        th.sort-active::after {
            content: '▼';
            font-size: 0.7em;
            margin-left: 5px;
            opacity: 0.7;
        }

        td {
            padding: 12px;
            text-align: center;
            background: var(--table-row-bg);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: #f0f0f0;
            transition: all 0.3s ease;
        }

        tr:hover td {
            background: var(--table-row-hover);
            color: white;
        }

        /* Scrollbar Styling */
        .table-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .table-wrapper::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }

        .table-wrapper::-webkit-scrollbar-thumb {
            background: rgba(106, 130, 251, 0.7);
            border-radius: 10px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .table-container {
                width: 100%;
                margin: 10px;
            }
            
            th, td {
                padding: 10px;
                font-size: 0.9em;
            }
        }

        /* Subtle Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        tbody tr {
            animation: fadeIn 0.5s ease backwards;
        }

        tbody tr:nth-child(even) {
            animation-delay: 0.1s;
        }

        tbody tr:nth-child(odd) {
            animation-delay: 0.2s;
        }
    </style>
</head>
<body>
    <div class="table-container">
        <div class="table-title">Student Results Dashboard</div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th class="<?php echo $sort_column == 'roll' ? 'sort-active' : ''; ?>">
                            <a href="?sort=roll">Registration</a>
                        </th>
                        <th class="<?php echo $sort_column == 'ename' ? 'sort-active' : ''; ?>">
                            <a href="?sort=ename">Student Name</a>
                        </th>
                        <th class="<?php echo $sort_column == 'marks' ? 'sort-active' : ''; ?>">
                            <a href="?sort=marks">Marks</a>
                        </th>
                        <th class="<?php echo $sort_column == 'percentage' ? 'sort-active' : ''; ?>">
                            <a href="?sort=percentage">Percentage</a>
                        </th>
                        <th class="<?php echo $sort_column == 'grade' ? 'sort-active' : ''; ?>">
                            <a href="?sort=grade">Grade</a>
                        </th>
                        <th class="<?php echo $sort_column == 'rank' ? 'sort-active' : ''; ?>">
                            <a href="?sort=rank">Rank</a>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>
                                    <td>{$row['roll']}</td>
                                    <td>{$row['ename']}</td>
                                    <td>{$row['marks']}</td>
                                    <td>{$row['percentage']}</td>
                                    <td>{$row['grade']}</td>
                                    <td>{$row['rank']}</td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='6'>No records found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    $conn->close();
    ?>
</body>
</html>