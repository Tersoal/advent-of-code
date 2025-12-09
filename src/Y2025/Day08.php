<?php

namespace App\Y2025;

use App\Model\DayBase;

class Day08 extends DayBase
{
    protected const int TEST_1 = 40;
    protected const int TEST_2 = 25272;

    protected const int LARGEST_CIRCUITS = 3;
    protected const int SHORTEST_CONNECTIONS_TEST = 10;
    protected const int SHORTEST_CONNECTIONS = 1000;

    private array $parent = [];
    private array $rank = [];

    public function loadData(string $filePath): void
    {
        $this->loadDataAsArray($filePath, "\n");

//        print_r($this->data);
//        echo PHP_EOL;
    }

    public function getResult(): array
    {
        return [
            $this->getPart1(),
            $this->getPart2()
        ];
    }

    private function getPart1(): int
    {
        $distances = $this->getPairDistances();
        asort($distances);
        $connections = array_slice($distances, 0, ($this->test ? self::SHORTEST_CONNECTIONS_TEST : self::SHORTEST_CONNECTIONS), true);

        if ($this->test) {
            print_r($distances);
            print_r($connections);
        }

        $circuits = $this->getCircuits($connections);
        $circuits = $this->sortCircuits($circuits);
        $largestCircuits = array_slice($circuits, 0, self::LARGEST_CIRCUITS);
        $counters = array_map(fn(array $circuitBoxes) => count($circuitBoxes), $largestCircuits);

        if ($this->test) {
            print_r($circuits);
            print_r($largestCircuits);
            print_r($counters);
        }

        return array_product($counters);
    }

    // CHatGPT help, with many iterations
    private function getPart2(): int
    {
//        $lastPair = $this->runKruskalOptimized($connections);
//        $lastPair = $this->runPrimStream($connections);
//        $lastPair = $this->runKruskalExternal($connections);
        $lastPair = $this->run($this->data);
        $lastComponents = [$lastPair['a'], $lastPair['b']];
        $lastX = [];

        foreach ($lastComponents as $component) {
            [$x, $y, $z] = explode(",", $component);
            $lastX[] = $x;
        }

        if ($this->test) {
            print_r($lastComponents);
            print_r($lastX);
        }

        return array_product($lastX);
    }

    private function getPairDistances(): array
    {
        $elementsCount = count($this->data);
        $pairDistances = [];

        for ($i = 0; $i < $elementsCount; $i++) {
            for ($j = 0; $j < $elementsCount; $j++) {
                if ($i === $j) {
                    continue;
                }

                $key = $this->data[$i] . '|' . $this->data[$j];
                $rKey = $this->data[$j] . '|' . $this->data[$i];

                if (isset($pairDistances[$key]) || isset($pairDistances[$rKey])) {
                    continue;
                }

                [$x1, $y1, $z1] = explode(',', $this->data[$i]);
                [$x2, $y2, $z2] = explode(',', $this->data[$j]);

                // Ref: https://en.wikipedia.org/wiki/Euclidean_distance
                $distance = sqrt(pow(($x1 - $x2), 2) + pow(($y1 - $y2), 2) + pow(($z1 - $z2), 2));

                $pairDistances[$key] = $distance;
            }
        }

        return $pairDistances;
    }

    // ChatGPT help for DFS
    private function getCircuits(array $connections): array
    {
        // 1. Construir grafo (lista de adyacencia)
        $graph = [];

        foreach ($connections as $key => $value) {
            list($a, $b) = explode('|', $key);

            $graph[$a][] = $b;
            $graph[$b][] = $a;
        }

        // 2. Buscar componentes conectados (DFS)
        $visited = [];
        $components = [];

        foreach ($graph as $node => $neighbors) {
            if (isset($visited[$node])) {
                continue;
            }

            // DFS iterativo
            $stack = [$node];
            $component = [];

            while ($stack) {
                $current = array_pop($stack);

                if (isset($visited[$current])) {
                    continue;
                }

                $visited[$current] = true;
                $component[] = $current;

                foreach ($graph[$current] as $nbr) {
                    if (!isset($visited[$nbr])) {
                        $stack[] = $nbr;
                    }
                }
            }

            $components[] = $component;
        }

        return $components;
    }

    private function sortCircuits(array $circuits, string $direction = 'DESC'): array
    {
        usort($circuits, fn(array $a, array $b) => count($a) <=> count($b));

        return $direction === 'DESC' ? array_reverse($circuits) : $circuits;
    }





//    // ------------------------------
//    // UNION-FIND OPTIMIZADO
//    // ------------------------------
//    private function ufInit(int $n): void
//    {
//        $this->parent = range(0, $n - 1);
//        $this->rank   = array_fill(0, $n, 0);
//    }
//
//    private function find(int $x): int
//    {
//        while ($this->parent[$x] !== $x) {
//            // Path halving (más rápido que path compression clásica)
//            $this->parent[$x] = $this->parent[$this->parent[$x]];
//            $x = $this->parent[$x];
//        }
//        return $x;
//    }
//
//    private function union(int $a, int $b): bool
//    {
//        $rootA = $this->find($a);
//        $rootB = $this->find($b);
//
//        if ($rootA === $rootB) return false;
//
//        if ($this->rank[$rootA] < $this->rank[$rootB]) {
//            $this->parent[$rootA] = $rootB;
//        } elseif ($this->rank[$rootA] > $this->rank[$rootB]) {
//            $this->parent[$rootB] = $rootA;
//        } else {
//            $this->parent[$rootB] = $rootA;
//            $this->rank[$rootA]++;
//        }
//
//        return true;
//    }
//
//    // ------------------------------
//    //  KRUSKAL OPTIMIZADO
//    // ------------------------------
//    public function runKruskalOptimized(array $pairs): array
//    {
//        // 1. Mapear coordenadas → índices enteros
//        $map = [];
//        $nodes = [];
//        $idx = 0;
//
//        foreach ($pairs as $key => $_) {
//            [$a, $b] = explode('|', $key);
//
//            if (!isset($map[$a])) {
//                $map[$a] = $idx;
//                $nodes[$idx] = $a;
//                $idx++;
//            }
//            if (!isset($map[$b])) {
//                $map[$b] = $idx;
//                $nodes[$idx] = $b;
//                $idx++;
//            }
//        }
//
//        $n = count($nodes);
//        $this->ufInit($n);
//        $sets = $n;
//
//        // 2. Crear lista compacta de aristas para Kruskal
//        $edges = [];
//        $i = 0;
//
//        foreach ($pairs as $key => $dist) {
//            [$a, $b] = explode('|', $key);
//            $edges[$i++] = [$map[$a], $map[$b], $dist];
//        }
//
//        // 3. Ordenar aristas por distancia ascendente
//        usort($edges, fn($x, $y) => $x[2] <=> $y[2]);
//
//        // 4. Ejecutar Kruskal
//        $lastEdge = null;
//
//        foreach ($edges as $edge) {
//            [$a, $b, $dist] = $edge;
//
//            if ($this->union($a, $b)) {
//                $sets--;
//                $lastEdge = $edge;
//
//                if ($sets === 1) break;
//            }
//        }
//
//        // 5. Devolver coordenadas reales
//        return [
//            'a' => $nodes[$lastEdge[0]],
//            'b' => $nodes[$lastEdge[1]],
//            'dist' => $lastEdge[2]
//        ];
//    }






//    private function ufInit(int $n): void
//    {
//        $this->parent = range(0, $n - 1);
//        $this->rank   = array_fill(0, $n, 0);
//    }
//
//    private function find(int $x): int
//    {
//        while ($this->parent[$x] !== $x) {
//            $this->parent[$x] = $this->parent[$this->parent[$x]];
//            $x = $this->parent[$x];
//        }
//        return $x;
//    }
//
//    private function union(int $a, int $b): bool
//    {
//        $rootA = $this->find($a);
//        $rootB = $this->find($b);
//
//        if ($rootA === $rootB) return false;
//
//        if ($this->rank[$rootA] < $this->rank[$rootB]) {
//            $this->parent[$rootA] = $rootB;
//        } elseif ($this->rank[$rootA] > $this->rank[$rootB]) {
//            $this->parent[$rootB] = $rootA;
//        } else {
//            $this->parent[$rootB] = $rootA;
//            $this->rank[$rootA]++;
//        }
//
//        return true;
//    }
//
//    public function runKruskalOptimized(array $pairs): array
//    {
//        $map = [];
//        $nodes = [];
//        $idx = 0;
//
//        // 1) Crear mapa de nodos sin explode()
//        foreach ($pairs as $key => $_) {
//            $pos = strpos($key, '|');
//            $a = substr($key, 0, $pos);
//            $b = substr($key, $pos + 1);
//
//            if (!isset($map[$a])) {
//                $map[$a] = $idx;
//                $nodes[$idx] = $a;
//                $idx++;
//            }
//            if (!isset($map[$b])) {
//                $map[$b] = $idx;
//                $nodes[$idx] = $b;
//                $idx++;
//            }
//        }
//
//        $n = count($nodes);
//        $this->ufInit($n);
//        $sets = $n;
//
//        // 2) Construir edges de forma limpia
//        $edges = [];
//        $i = 0;
//
//        foreach ($pairs as $key => $dist) {
//            $pos = strpos($key, '|');
//
//            $a = substr($key, 0, $pos);
//            $b = substr($key, $pos + 1);
//
//            $edges[$i++] = [$map[$a], $map[$b], $dist];
//        }
//
//        // Liberar pairs (ya no lo necesitamos)
//        unset($pairs);
//
//        // 3) Ordenar aristas
//        usort($edges, fn($x, $y) => $x[2] <=> $y[2]);
//
//        // 4) Kruskal
//        $lastEdge = null;
//
//        foreach ($edges as $edge) {
//            [$a, $b, $dist] = $edge;
//
//            if ($this->union($a, $b)) {
//                $sets--;
//                $lastEdge = $edge;
//                if ($sets === 1) break;
//            }
//        }
//
//        return [
//            'a' => $nodes[$lastEdge[0]],
//            'b' => $nodes[$lastEdge[1]],
//            'dist' => $lastEdge[2]
//        ];
//    }






//    public function runPrimStream(array $pairs): array
//    {
//        // Mapear nodos
//        $map = [];
//        $nodes = [];
//        $idx = 0;
//
//        foreach ($pairs as $key => $_) {
//            $pos = strpos($key, '|');
//            $a = substr($key, 0, $pos);
//            $b = substr($key, $pos + 1);
//
//            if (!isset($map[$a])) {
//                $map[$a] = $idx;
//                $nodes[$idx] = $a;
//                $idx++;
//            }
//            if (!isset($map[$b])) {
//                $map[$b] = $idx;
//                $nodes[$idx] = $b;
//                $idx++;
//            }
//        }
//
//        $n = count($nodes);
//
//        // Estructura: grafo como listas de adyacencia
//        // pero SIN duplicar todo el input:
//        $graph = array_fill(0, $n, []);
//
//        foreach ($pairs as $key => $dist) {
//            $pos = strpos($key, '|');
//            $a = substr($key, 0, $pos);
//            $b = substr($key, $pos + 1);
//
//            $ia = $map[$a];
//            $ib = $map[$b];
//
//            $graph[$ia][] = [$ib, $dist];
//            $graph[$ib][] = [$ia, $dist];
//        }
//
//        // liberar memoria
//        unset($pairs);
//
//        // PRIM
//        $visited = array_fill(0, $n, false);
//        $pq = new \SplPriorityQueue();
//        $pq->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
//
//        $start = 0;
//        $visited[$start] = true;
//
//        foreach ($graph[$start] as $edge) {
//            [$to, $dist] = $edge;
//            $pq->insert([$start, $to, $dist], -$dist);
//        }
//
//        $edgesUsed = 0;
//        $lastEdge = null;
//
//        while (!$pq->isEmpty() && $edgesUsed < $n - 1) {
//            [$a, $b, $dist] = $pq->extract();
//
//            if ($visited[$b]) {
//                continue;
//            }
//
//            $visited[$b] = true;
//            $edgesUsed++;
//            $lastEdge = [$a, $b, $dist];
//
//            foreach ($graph[$b] as $edge) {
//                [$to, $d] = $edge;
//                if (!$visited[$to]) {
//                    $pq->insert([$b, $to, $d], -$d);
//                }
//            }
//        }
//
//        return [
//            'a' => $nodes[$lastEdge[0]],
//            'b' => $nodes[$lastEdge[1]],
//            'dist' => $lastEdge[2],
//        ];
//    }






//    private function ufInit(int $n): void
//    {
//        $this->parent = range(0, $n - 1);
//        $this->rank   = array_fill(0, $n, 0);
//    }
//
//    private function find(int $x): int
//    {
//        while ($this->parent[$x] !== $x) {
//            $this->parent[$x] = $this->parent[$this->parent[$x]];
//            $x = $this->parent[$x];
//        }
//        return $x;
//    }
//
//    private function union(int $a, int $b): bool
//    {
//        $ra = $this->find($a);
//        $rb = $this->find($b);
//        if ($ra === $rb) return false;
//
//        if ($this->rank[$ra] < $this->rank[$rb]) {
//            $this->parent[$ra] = $rb;
//        } elseif ($this->rank[$ra] > $this->rank[$rb]) {
//            $this->parent[$rb] = $ra;
//        } else {
//            $this->parent[$rb] = $ra;
//            $this->rank[$ra]++;
//        }
//        return true;
//    }
//
//    /**
//     * Ejecuta Kruskal usando external sort para manejar entradas muy grandes.
//     *
//     * @param iterable $pairs Iterable que produce "A|B" => distance
//     * @param int $chunkLines Número de líneas por chunk en la fase de ordenación (ajustable)
//     * @return array ['a' => coordA, 'b' => coordB, 'dist' => float]
//     */
//    public function runKruskalExternal(iterable $pairs, int $chunkLines = 200000): array
//    {
//        $tmpDir = sys_get_temp_dir();
//        $edgeTemp = tempnam($tmpDir, 'edges_');
//        $fhEdge = fopen($edgeTemp, 'w+');
//        if ($fhEdge === false) {
//            throw new \RuntimeException("No se puede crear fichero temporal para aristas");
//        }
//
//        // 1) Primera pasada: mapear nodos y volcar lines "dist<TAB>a<TAB>b\n"
//        $map = [];
//        $nodes = [];
//        $nextIdx = 0;
//
//        foreach ($pairs as $key => $dist) {
//            $pos = strpos($key, '|');
//            $a = substr($key, 0, $pos);
//            $b = substr($key, $pos + 1);
//
//            if (!isset($map[$a])) {
//                $map[$a] = $nextIdx;
//                $nodes[$nextIdx] = $a;
//                $nextIdx++;
//            }
//            if (!isset($map[$b])) {
//                $map[$b] = $nextIdx;
//                $nodes[$nextIdx] = $b;
//                $nextIdx++;
//            }
//
//            // formato: dist \t a \t b \n
//            fwrite($fhEdge, (string)$dist . "\t" . $a . "\t" . $b . PHP_EOL);
//        }
//
//        if (is_array($pairs)) {
//            unset($pairs); // liberar memoria
//        }
//
//        fflush($fhEdge);
//        fclose($fhEdge);
//
//        $n = count($nodes);
//        if ($n === 0) {
//            throw new \RuntimeException("No hay nodos en la entrada");
//        }
//
//        // 2) Dividir edgeTemp en chunks ordenados
//        $chunkFiles = [];
//        $fhEdge = fopen($edgeTemp, 'r');
//        if ($fhEdge === false) {
//            throw new \RuntimeException("No se puede abrir fichero temporal de aristas");
//        }
//
//        $linesBuf = [];
//        $count = 0;
//        while (!feof($fhEdge)) {
//            $line = fgets($fhEdge);
//            if ($line === false) break;
//            $line = rtrim($line, "\r\n");
//            if ($line === '') continue;
//
//            $linesBuf[] = $line;
//            $count++;
//
//            if ($count >= $chunkLines) {
//                $chunkFiles[] = $this->writeSortedChunk($linesBuf);
//                $linesBuf = [];
//                $count = 0;
//            }
//        }
//        if ($count > 0) {
//            $chunkFiles[] = $this->writeSortedChunk($linesBuf);
//            $linesBuf = [];
//        }
//        fclose($fhEdge);
//        @unlink($edgeTemp);
//
//        // 3) Preparar Union-Find
//        $this->ufInit($n);
//        $sets = $n;
//
//        // 4) K-way merge de chunks (heap) y Kruskal streaming
//        $chunkHandles = [];
//        foreach ($chunkFiles as $i => $cf) {
//            $h = fopen($cf, 'r');
//            if ($h === false) throw new \RuntimeException("No se puede abrir chunk $cf");
//            $chunkHandles[$i] = $h;
//        }
//
//        $pq = new \SplPriorityQueue();
//        $pq->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
//
//        // leer primera línea de cada chunk
//        foreach ($chunkHandles as $i => $h) {
//            $line = fgets($h);
//            if ($line === false) continue;
//            $line = rtrim($line, "\r\n");
//            if ($line === '') {
//                while (($line = fgets($h)) !== false && trim($line) === '') {}
//                if ($line === false) continue;
//                $line = rtrim($line, "\r\n");
//            }
//            [$dist, $a, $b] = $this->parseEdgeLine($line);
//            $pq->insert(['dist' => $dist, 'a' => $a, 'b' => $b, 'chunk' => $i], -$dist);
//        }
//
//        $lastEdge = null;
//
//        while (!$pq->isEmpty()) {
//            $item = $pq->extract();
//            $dist = $item['dist'];
//            $aS = $item['a'];
//            $bS = $item['b'];
//            $chunkIndex = $item['chunk'];
//
//            $ia = $map[$aS];
//            $ib = $map[$bS];
//
//            if ($this->union($ia, $ib)) {
//                $sets--;
//                $lastEdge = [$aS, $bS, $dist];
//                if ($sets === 1) break;
//            }
//
//            // push siguiente línea del mismo chunk
//            $h = $chunkHandles[$chunkIndex];
//            $line = fgets($h);
//            if ($line !== false) {
//                $line = rtrim($line, "\r\n");
//                if ($line !== '') {
//                    [$nd, $na, $nb] = $this->parseEdgeLine($line);
//                    $pq->insert(['dist' => $nd, 'a' => $na, 'b' => $nb, 'chunk' => $chunkIndex], -$nd);
//                } else {
//                    while (($line = fgets($h)) !== false) {
//                        $line = rtrim($line, "\r\n");
//                        if ($line !== '') {
//                            [$nd, $na, $nb] = $this->parseEdgeLine($line);
//                            $pq->insert(['dist' => $nd, 'a' => $na, 'b' => $nb, 'chunk' => $chunkIndex], -$nd);
//                            break;
//                        }
//                    }
//                }
//            }
//        }
//
//        foreach ($chunkHandles as $i => $h) {
//            fclose($h);
//            @unlink($chunkFiles[$i]);
//        }
//
//        if ($lastEdge === null) {
//            throw new \RuntimeException("No se encontró ninguna arista válida");
//        }
//
//        return [
//            'a' => $lastEdge[0],
//            'b' => $lastEdge[1],
//            'dist' => $lastEdge[2]
//        ];
//    }
//
//    // writeSortedChunk espera $lines como array de strings "dist\tA\tB"
//    // devuelve path del fichero chunk ordenado por dist asc.
//    private function writeSortedChunk(array $lines): string
//    {
//        $rows = [];
//        foreach ($lines as $ln) {
//            $rows[] = $this->parseEdgeLine($ln); // devuelve [dist, a, b]
//        }
//
//        // ordenar por dist (índice 0)
//        usort($rows, function ($x, $y) {
//            if ($x[0] == $y[0]) return 0;
//            return ($x[0] < $y[0]) ? -1 : 1;
//        });
//
//        $tmp = tempnam(sys_get_temp_dir(), 'chunk_');
//        $fh = fopen($tmp, 'w');
//        if ($fh === false) throw new \RuntimeException("No se puede crear chunk temp");
//        foreach ($rows as $r) {
//            // r = [dist, a, b]
//            fwrite($fh, (string)$r[0] . "\t" . $r[1] . "\t" . $r[2] . PHP_EOL);
//        }
//        fclose($fh);
//        return $tmp;
//    }
//
//    // parsea "dist\tA\tB" -> [dist (float), A (string), B (string)]
//    private function parseEdgeLine(string $line): array
//    {
//        $p1 = strpos($line, "\t");
//        $p2 = strpos($line, "\t", $p1 + 1);
//        if ($p1 === false || $p2 === false) {
//            throw new \RuntimeException("Formato de línea inválido: $line");
//        }
//        $dist = (float) substr($line, 0, $p1);
//        $a = substr($line, $p1 + 1, $p2 - ($p1 + 1));
//        $b = substr($line, $p2 + 1);
//        return [$dist, $a, $b];
//    }




//    private function ufInit(int $n): void
//    {
//        $this->parent = range(0, $n - 1);
//        $this->rank = array_fill(0, $n, 0);
//    }
//
//    private function find(int $x): int
//    {
//        while ($this->parent[$x] !== $x) {
//            $this->parent[$x] = $this->parent[$this->parent[$x]]; // path halving
//            $x = $this->parent[$x];
//        }
//        return $x;
//    }
//
//    private function union(int $a, int $b): bool
//    {
//        $ra = $this->find($a);
//        $rb = $this->find($b);
//        if ($ra === $rb) return false;
//
//        if ($this->rank[$ra] < $this->rank[$rb]) {
//            $this->parent[$ra] = $rb;
//        } elseif ($this->rank[$ra] > $this->rank[$rb]) {
//            $this->parent[$rb] = $ra;
//        } else {
//            $this->parent[$rb] = $ra;
//            $this->rank[$ra]++;
//        }
//        return true;
//    }
//
//    private function generateEdges(array $points): iterable
//    {
//        $n = count($points);
//        for ($i = 0; $i < $n; $i++) {
//            $p1 = explode(',', $points[$i]);
//            for ($j = $i + 1; $j < $n; $j++) {
//                $p2 = explode(',', $points[$j]);
//                $dx = $p1[0] - $p2[0];
//                $dy = $p1[1] - $p2[1];
//                $dz = $p1[2] - $p2[2];
//                $dist = sqrt($dx*$dx + $dy*$dy + $dz*$dz);
//                yield [$i, $j, $dist];
//            }
//        }
//    }
//
//    public function run(array $points): array
//    {
//        $n = count($points);
//        $this->ufInit($n);
//
//        // Usamos SplPriorityQueue como min-heap
//        $pq = new \SplPriorityQueue();
//        $pq->setExtractFlags(\SplPriorityQueue::EXTR_DATA);
//
//        foreach ($this->generateEdges($points) as $edge) {
//            [$i, $j, $dist] = $edge;
//            $pq->insert([$i, $j, $dist], -$dist); // -dist para min-heap
//        }
//
//        $sets = $n;
//        $lastEdge = null;
//        while (!$pq->isEmpty()) {
//            [$i, $j, $dist] = $pq->extract();
//            if ($this->union($i, $j)) {
//                $sets--;
//                $lastEdge = [$i, $j, $dist];
//                if ($sets === 1) break;
//            }
//        }
//
//        return [
//            'a' => $points[$lastEdge[0]],
//            'b' => $points[$lastEdge[1]],
//            'dist' => $lastEdge[2]
//        ];
//    }






    private function ufInit(int $n): void
    {
        $this->parent = range(0, $n - 1);
        $this->rank = array_fill(0, $n, 0);
    }

    private function find(int $x): int
    {
        while ($this->parent[$x] !== $x) {
            $this->parent[$x] = $this->parent[$this->parent[$x]];
            $x = $this->parent[$x];
        }
        return $x;
    }

    private function union(int $a, int $b): bool
    {
        $ra = $this->find($a);
        $rb = $this->find($b);
        if ($ra === $rb) return false;

        if ($this->rank[$ra] < $this->rank[$rb]) {
            $this->parent[$ra] = $rb;
        } elseif ($this->rank[$ra] > $this->rank[$rb]) {
            $this->parent[$rb] = $ra;
        } else {
            $this->parent[$rb] = $ra;
            $this->rank[$ra]++;
        }
        return true;
    }

    /**
     * Ejecuta Kruskal para muchos puntos usando chunks y k-way merge.
     *
     * @param array $points Lista de coordenadas ["x,y,z", ...]
     * @param int $chunkLines Número de aristas por chunk (ajustable según RAM)
     * @return array ['a' => string, 'b' => string, 'dist' => float]
     */
    public function run(array $points, int $chunkLines = 100000): array
    {
        $n = count($points);
        $this->ufInit($n);

        $tmpDir = sys_get_temp_dir();
        $chunkFiles = [];

        // 1) Generar aristas en chunks y ordenarlas
        $buffer = [];
        for ($i = 0; $i < $n; $i++) {
            $p1 = explode(',', $points[$i]);
            for ($j = $i + 1; $j < $n; $j++) {
                $p2 = explode(',', $points[$j]);
                $dx = $p1[0]-$p2[0];
                $dy = $p1[1]-$p2[1];
                $dz = $p1[2]-$p2[2];
                $dist = sqrt($dx*$dx + $dy*$dy + $dz*$dz);
                $buffer[] = [$dist, $i, $j];
                if (count($buffer) >= $chunkLines) {
                    $chunkFiles[] = $this->writeSortedChunk($buffer);
                    $buffer = [];
                }
            }
        }
        if (count($buffer) > 0) {
            $chunkFiles[] = $this->writeSortedChunk($buffer);
            $buffer = [];
        }

        // 2) K-way merge y Kruskal streaming
        $sets = $n;
        $lastEdge = null;

        $handles = [];
        $heap = new \SplMinHeap();
        foreach ($chunkFiles as $k => $file) {
            $h = fopen($file, 'r');
            $handles[$k] = $h;
            if (($line = fgets($h)) !== false) {
                $line = rtrim($line, "\r\n");
                [$dist, $i, $j] = explode("\t", $line);
                $heap->insert([(float)$dist, (int)$i, (int)$j, $k]);
            }
        }

        while (!$heap->isEmpty()) {
            [$dist, $i, $j, $chunk] = $heap->extract();
            if ($this->union($i, $j)) {
                $sets--;
                $lastEdge = [$i, $j, $dist];
                if ($sets === 1) break;
            }

            // leer siguiente línea del mismo chunk
            if (($line = fgets($handles[$chunk])) !== false) {
                $line = rtrim($line, "\r\n");
                [$ndist, $ni, $nj] = explode("\t", $line);
                $heap->insert([(float)$ndist, (int)$ni, (int)$nj, $chunk]);
            }
        }

        // cerrar y borrar chunks
        foreach ($handles as $h) fclose($h);
        foreach ($chunkFiles as $f) @unlink($f);

        return [
            'a' => $points[$lastEdge[0]],
            'b' => $points[$lastEdge[1]],
            'dist' => $lastEdge[2]
        ];
    }

    private function writeSortedChunk(array $edges): string
    {
        usort($edges, fn($a, $b) => $a[0] <=> $b[0]); // ordenar por distancia
        $file = tempnam(sys_get_temp_dir(), 'chunk_');
        $fh = fopen($file, 'w');
        foreach ($edges as [$dist, $i, $j]) {
            fwrite($fh, "$dist\t$i\t$j\n");
        }
        fclose($fh);
        return $file;
    }
}
