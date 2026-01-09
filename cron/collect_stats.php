<?php
require_once __DIR__ . '/../php/connect.php';
set_time_limit(0);

try {
    $db = new db();
    $pdo = $db->connect();

    $cmd = "docker stats --no-stream --format \"{{json .}}\"";
    $output = shell_exec($cmd);

    if (!$output) {
        echo "[" . date('Y-m-d H:i:s') . "] No running containers found.\n";
        exit(0);
    }

    $lines = explode("\n", trim($output));
    
    $sql = "INSERT INTO container_stats (container_name, cpu_perc, mem_usage, net_io, block_io, pids, updated_at) 
            VALUES (:name, :cpu, :mem, :net, :block, :pids, NOW())
            ON DUPLICATE KEY UPDATE 
            cpu_perc = VALUES(cpu_perc), 
            mem_usage = VALUES(mem_usage), 
            net_io = VALUES(net_io), 
            block_io = VALUES(block_io), 
            pids = VALUES(pids), 
            updated_at = NOW()";
            
    $stmt = $pdo->prepare($sql);
    $count = 0;

    foreach ($lines as $line) {
        if (empty(trim($line))) continue;

        $stat = json_decode($line, true);
        
        if ($stat) {
            $name = $stat['Name'] ?? $stat['name'] ?? null;
            
            if ($name) {
                $stmt->execute([
                    ':name' => $name,
                    ':cpu' => $stat['CPUPerc'] ?? $stat['cpu_perc'] ?? '0%',
                    ':mem' => $stat['MemUsage'] ?? $stat['mem_usage'] ?? '0MB',
                    ':net' => $stat['NetIO'] ?? $stat['net_io'] ?? '0B',
                    ':block' => $stat['BlockIO'] ?? $stat['block_io'] ?? '0B',
                    ':pids' => $stat['PIDs'] ?? $stat['pids'] ?? '0'
                ]);
                $count++;
            }
        }
    }

    echo "[" . date('Y-m-d H:i:s') . "] updated stats for $count containers.\n";

} catch (Exception $e) {
    echo "[" . date('Y-m-d H:i:s') . "] Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>
