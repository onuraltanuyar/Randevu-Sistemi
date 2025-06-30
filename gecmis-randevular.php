<?php
$page_title = 'Geçmiş Randevular';
require_once 'panel-header.php';

$stmt = $conn->prepare(
    "SELECT appointment_date, status, notes FROM appointments 
     WHERE user_id = ? AND (appointment_date < CURDATE() OR status IN ('canceled', 'completed'))
     ORDER BY appointment_date DESC"
);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container">
    <h1 class="page-title">Geçmiş Randevular</h1>
    
    <table>
        <thead>
            <tr>
                <th>Randevu Tarihi</th>
                <th>Randevu Saati</th>
                <th>Durum</th>
                <th>Notlar</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): 
                    $date = new DateTime($row['appointment_date']);
                ?>
                <tr>
                    <td><?php echo $date->format('d-m-Y'); ?></td>
                    <td><?php echo $date->format('H:i'); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                    <td><?php echo htmlspecialchars($row['notes']); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4" style="text-align:center;">Gösterilecek geçmiş randevunuz bulunmamaktadır.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>