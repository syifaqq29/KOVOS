<!DOCTYPE html>
<html>

<head>
    <title>Staff List</title>
</head>

<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; padding: 30px;">

    <div
        style="max-width: 1000px; margin: auto; background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="text-align: center; margin-bottom: 20px;">Staff Registry</h2>

        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">No</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Name</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">IC</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Plate</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Vehicle</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Status</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Barcode</th>
                    <th style="padding: 12px; border: 1px solid #ccc; background-color: #eee;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Select only staff from the database
                $papar = mysqli_query($conn, "SELECT * FROM user WHERE status = 'staff' ORDER BY nama ASC");
                $no = 1; // Counter for numbering
                
                while ($row = mysqli_fetch_array($papar)) {
                    echo "<tr>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>" . $no++ . "</td>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($row['nama']) . "</strong></td>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['no_kp']) . "</td>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd;'><code style='background: #f0f0f0; padding: 2px 6px; border-radius: 3px;'>" . htmlspecialchars($row['no_plat']) . "</code></td>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['kenderaan']) . "</td>";
                    echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($row['status']) . "</span></td>";

                    // Barcode download link - FIXED VERSION
                    if (!empty($row['barcode_img'])) {
                        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>";
                        // Option 1: Direct download via PHP script
                        echo "<a href='download_barcode.php?file=" . urlencode($row['barcode_img']) . "&name=" . urlencode($row['nama']) . "'  color: white; padding: 6px 12px; border-radius: 6px; font-size: 0.9em;'>Download</a>";
                        echo "</td>";
                    } else {
                        echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center; color: #999;'>No Barcode</td>";
                    }

                    echo "<td style='padding: 10px; border: 1px solid #ddd; text-align: center;'>";
                    echo "<a href='delete.php?no_kp=" . htmlspecialchars($row['no_kp']) . "' onclick=\"return confirm(' Are you sure you want to delete this staff record? This action cannot be undone.')\"  color: white; padding: 6px 12px; border-radius: 6px; font-size: 0.9em;'>Delete</a>";
                    echo "</td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        // Alternative JavaScript method (if you prefer to keep JS approach)
        function downloadBarcodeJS(barcodeFileName, staffName) {
            if (!barcodeFileName) {
                alert('No barcode file available for this staff.');
                return;
            }

            // Try different possible paths
            const possiblePaths = [
                'barcodes/' + barcodeFileName,           // Current directory/barcodes
                '../barcodes/' + barcodeFileName,        // Parent directory/barcodes  
                './uploads/barcodes/' + barcodeFileName, // uploads/barcodes
                './assets/barcodes/' + barcodeFileName,  // assets/barcodes
                './' + barcodeFileName                   // Same directory as this file
            ];

            // Test if file exists and download
            testAndDownload(possiblePaths, 0, staffName, barcodeFileName);
        }

        function testAndDownload(paths, index, staffName, fileName) {
            if (index >= paths.length) {
                alert('❌ Barcode file not found. Please check the file path configuration.');
                return;
            }

            const img = new Image();
            img.onload = function () {
                // File exists, proceed with download
                const link = document.createElement('a');
                link.href = paths[index];
                const cleanName = staffName.replace(/[^a-zA-Z0-9]/g, '_');
                const fileExtension = fileName.split('.').pop();
                link.download = 'Barcode_' + cleanName + '.' + fileExtension;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            };
            img.onerror = function () {
                // Try next path
                testAndDownload(paths, index + 1, staffName, fileName);
            };
            img.src = paths[index];
        }

        // Add hover effects to table rows
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function () {
                    this.style.backgroundColor = '#f8f9fa';
                });
                row.addEventListener('mouseleave', function () {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>

    <style>
        /* Add some responsive design */
        @media (max-width: 768px) {
            body {
                padding: 15px;
            }

            table {
                font-size: 0.9em;
            }

            th,
            td {
                padding: 8px !important;
            }

            .container {
                padding: 15px;
            }
        }

        /* Hover effects for buttons */
        a[style*="background: #28a745"]:hover {
            background: #218838 !important;
        }

        a[style*="background: #dc3545"]:hover {
            background: #c82333 !important;
        }

        /* Table row hover effect */
        tbody tr:hover {
            background-color: #f8f9fa !important;
        }
    </style>

</body>

</html>