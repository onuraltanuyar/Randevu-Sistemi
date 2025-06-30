<?php
$page_title = 'Randevu Takvimi';

require_once 'panel-header.php';

$stmt = $conn->prepare(
    "SELECT appointment_date, status FROM appointments 
     WHERE appointment_date >= CURDATE() AND status IN ('pending', 'confirmed')
     ORDER BY appointment_date ASC"
);
$stmt->execute();
$result = $stmt->get_result();

$grouped_appointments = [];
while ($row = $result->fetch_assoc()) {
    $date = new DateTime($row['appointment_date']);
    $day = $date->format('Y-m-d');
    $grouped_appointments[$day][] = $row;
}
$stmt->close();
?>

<div class="container">
    <h1 class="page-title">Randevu Takvimi</h1>
    <p class="page-subtitle">Aşağıda dolu olan zaman dilimlerini görebilirsiniz. Yeni bir randevu almak için "Kullanıcı Paneli"nden ilgili menüyü kullanabilirsiniz.</p>

    <?php if (empty($grouped_appointments)): ?>
        <p style="text-align:center; font-size: 1.2em; color: var(--accent);">Tüm randevular müsaittir!</p>
    <?php else: ?>
        <?php foreach ($grouped_appointments as $day => $appointments): 
            $day_obj = new DateTime($day);
        ?>
            <div class="day-group">
                <h2><?php echo $day_obj->format('d F Y, l'); ?></h2>
                <?php foreach ($appointments as $appointment): 
                    $time_obj = new DateTime($appointment['appointment_date']);
                ?>
                    <div class="timeslot">
                        <span class="time"><i class="far fa-clock"></i> <?php echo $time_obj->format('H:i:'); ?></span>
                        <span class="status">Dolu</span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</body>
</html>