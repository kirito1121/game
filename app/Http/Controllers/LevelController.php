<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;

class LevelController extends Controller
{
    public function convertFileLv($level)
    {

        $rs = $level->data;
        // su ly data tren map
        $mrs = strstr($rs, "i");
        $mrs1 = strstr($mrs, "[");
        $mrs2 = strstr($mrs1, "]");
        $mapR = trim(str_replace($mrs2, ' ', $mrs1));
        $map = explode('|', substr($mapR, 1, strlen($mapR)));

        // su ly train path json
        $trainjs = strstr($rs, "j");
        $trainjsm = strstr($trainjs, "[");
        $trainjsm2 = strstr($trainjsm, "]");
        $trainjsM = trim(str_replace($trainjsm2, ' ', $trainjsm));
        $trainjsRS = explode(',', substr($trainjsM, 1, strlen($trainjsM)));

        // su ly tram path json
        $tramjs = strstr($rs, "l");
        $tramjsm = strstr($tramjs, "[");
        $tramjsm2 = strstr($tramjsm, "],");
        $tramjsM = trim(str_replace($tramjsm2, ' ', $tramjsm));
        $tramjsRS = explode('|', substr($tramjsM, 1, strlen($tramjsM)));
        foreach ($tramjsRS as $key => $item) {
            $tramjsRS[$key] = explode(',', substr($item, 1, (strlen($item) - 2)));
        }

        $variant = strstr($rs, "p");
        $variant1 = strstr($variant, "[");
        $variant2 = strstr($variant1, "]");
        $variantA = trim(str_replace($variant2, ' ', $variant1));
        $variant = explode('|', substr($variantA, 1, strlen($variantA)));
        // Log::info($tramjsRS);

        // chuyen chuoi thanh mang
        $rs = str_replace('[', '', $rs);
        $rs = str_replace(']', '', $rs);
        $array = explode(',', $rs);
        $data = [];
        // su ly mang dua ra du lieu mong muon
        foreach ($array as $value) {
            $arr = explode(':', $value);
            // return $arr;
            if (count($arr) > 1) {
                if ($arr[0] == 'i') {
                    $arr[1] = $map;
                }
                if ($arr[0] == 'j') {
                    $arr[1] = $trainjsRS;
                }
                if ($arr[0] == 'l') {
                    $arr[1] = $tramjsRS;
                }
                if ($arr[0] == 'p') {
                    $arr[1] = $variant;
                }
                $a = [$arr[0] => $arr[1]];
                $data = array_merge($data, $a);
            }
        }
        // Log::info($data);
        return $data;

    }

    public function viewLevel(Request $request)
    {
        $level = $request->get('level');
        $levelVersion = $request->get('levelVersion');
        $appVersion = $request->get('appVersion');
        $levelType = intval($request->get('levelType'));
        $versions = ["1.2.11", "1.4.1.0", "1.4.2.0", "1.4.2.1", "1.4.3", "1.4.4", "1.4.5", "1.4.6", "1.4.7", "1.4.8"];

        $data = $this->readDataMap($level, $levelType, $levelVersion, $appVersion);

        return view('levels.level', compact('data', 'level', 'levelType', 'versions', 'appVersion', 'levelVersion'));
    }

    public function readDataMap($level, $levelType = null, $levelVersion = null, $appVersion = null)
    {
        if (!isset($level)) {
            return [];
        }

        if ($levelType === 0) {
            $type = 'Saga';
        } else if ($levelType === 1) {
            $type = 'RewardRush/ChallengeRace';
        } else {
            $type = "EventX";
        }

        $levels = DB::connection('mysqluserDB')->table('levels')
            ->when($level, function ($query) use ($level) {
                $query->where('level', $level);
            })
            ->when($levelVersion, function ($query) use ($levelVersion) {
                $query->where('levelVersion', $levelVersion);
            })
            ->when($appVersion, function ($query) use ($appVersion) {
                $query->where('AppVersion', $appVersion);
            })
            ->when($levelType >= 0, function ($query) use ($type) {
                $query->where('levelType', $type);
            })
            ->orderBy('sublevel', 'ASC')
            ->orderBy('levelVersion', 'DESC')
            ->get();

        $array = [];

        foreach ($levels as $level) {
            $data = $this->convertFileLv($level);
            $data['level'] = $level->level;
            $data['sublevel'] = $level->sublevel;
            $data['levelVersion'] = $level->levelVersion;
            $data['appVersion'] = $level->appVersion;
            $data['levelType'] = $level->levelType;
            $data['trackingDataLevelTest'] = $this->trackingDataLevelTest($level->level, $level->levelVersion, $level->sublevel);
            $data['trackingDataLevelReleases'] = $this->trackingDataLevelReleases($level->level, $level->levelVersion, $level->sublevel);
            $item = $data['i'];
            $arrm = [];
            foreach ($item as $key => $it) {
                $temp = explode(',', $it);
                $arrm = array_merge($arrm, $temp);

            }
            $item = $arrm;
            foreach ($item as $key => $it) {

                $tmp = array_search(intval(str_replace(strstr($it, "_"), "", $it)), config('entity.entityType'));
                $item[$key] = $it == 42 ? "Car" : $tmp;
            }

            $obs = array_count_values(array_diff($item, [""]));

            $data['obs'] = $obs;

            array_push($array, $data);
        }
        return $array;
    }

    public function checkFile()
    {
        $files = count(Storage::disk('local')->allFiles('public/Levels'));
        $insertData = [];

        $levels = DB::connection('mysqluserDB')->table('levels')->get();
        $collect = collect($levels);
        for ($i = 1; $i <= $files; $i++) {
            if (file_exists(storage_path("levels\Level\/$i.bytes"))) {
                $content = trim(strstr(file_get_contents(storage_path("levels\Level\/$i.bytes")), "["));
                $rs = trim(substr($content, 1, strlen($content) - 2));
                $levels = $collect->where('Level', $i);
                if ($levels) {
                    $array_data = $levels->pluck('Data');
                    if (!in_array($rs, $array_data->toArray())) {
                        array_push($insertData, ['Level' => $i, 'Data' => $rs, 'Version' => count($levels) + 1, 'DateTime' => now()]);
                    }
                } else {
                    array_push($insertData, ['Level' => $i, 'Data' => $rs, 'Version' => 1, 'DateTime' => now()]);
                }
            }
        }
        $levels = DB::connection('mysqluserDB')->table('levels')->insert($insertData);
        return "done";
    }

    public function viewLevelTable()
    {
        $inputFileName = storage_path('Levels\Level.xlsx');
        $spreadsheet = IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        return view('levels.levelTable', compact('sheetData'));
    }

    public function pushLevel(Request $request)
    {
        try {
            $data = $request->data;
            $level = $request->level;
            $subLevel = $request->sublevel ?? 0;
            $levelVersion = $request->levelVersion;
            $appVersion = $request->appVersion;
            $levelType = intval($request->levelType);

            $type = null;
            if ($levelType == 0) {
                $type = 'Saga';
            } else if ($levelType == 1) {
                $type = 'RewardRush/ChallengeRace';
            } else {
                $type = "EventX";
            }
            $lv = DB::connection('mysqluserDB')->table('levels')
                ->where('level', $level)
                ->where('sublevel', $subLevel)
                ->where('levelVersion', $levelVersion)
                ->where('levelType', $type)->first();
            if (!$lv) {
                DB::connection('mysqluserDB')->table('levels')->insert([
                    [
                        'level' => $level,
                        'sublevel' => $subLevel,
                        'levelVersion' => $levelVersion,
                        'AppVersion' => $appVersion,
                        'data' => $data,
                        'levelType' => $type,
                    ],
                ]);
                Log::info("Luu moi");
                return "Lưu mới";
            } else {
                if (strcmp($lv->data, $data) !== 0) {
                    DB::connection('mysqluserDB')->table('levels')->insert([
                        [
                            'level' => $level,
                            'sublevel' => $subLevel,
                            'levelVersion' => $levelVersion,
                            'AppVersion' => $appVersion,
                            'data' => $data,
                            'levelType' => $type,
                        ],
                    ]);
                    Log::info("Luu moi version");
                    return "Lưu mới version";
                }
            }
            Log::info("Không lưu");
            return "Không lưu";
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function pushLevels(Request $request)
    {
        try {
            $data = json_decode(trim($request->data));
            $levelType = $request->levelType;
            $type = null;
            if ($levelType == 0) {
                $type = 'Saga';
            } else if ($levelType == 1) {
                $type = 'Rush';
            } else {
                $type = "EventX";
            }
            $lvs = collect(DB::connection('mysqluserDB')->table('levels')->where('levelType', $type)->get());
            $arrayInsert = [];
            foreach ($data as $item) {
                if (count($lvs) > 0) {
                    $levels = $lvs->where('level', $item->level);
                    if (count($levels) > 0) {
                        if (!in_array($item->data, $levels->pluck('data')->toArray())) {
                            array_push($arrayInsert, [
                                'level' => $item->level,
                                'sublevel' => $item->sublevel,
                                'levelVersion' => $item->levelVersion,
                                'appVersion' => $item->appVersion,
                                'data' => $item->data,
                                'levelType' => $type,
                            ]);
                        }
                    }
                } else {
                    array_push($arrayInsert,
                        [
                            'level' => $item->level,
                            'sublevel' => $item->sublevel,
                            'levelVersion' => $item->levelVersion,
                            'appVersion' => $item->appVersion,
                            'data' => $item->data,
                            'levelType' => $type,
                        ]
                    );
                }
            }

            if (count($arrayInsert) > 0) {
                DB::connection('mysqluserDB')->table('levels')->insert($arrayInsert);
            }
            return $arrayInsert;
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getLevel(Request $request)
    {
        try {
            $level = $request->get('level');
            $sublevel = $request->get('sublevel');
            $levelVersion = $request->get('levelVersion');
            $appVersion = $request->get('appVersion');
            $levelType = intval($request->get('levelType'));
            $type = null;
            if ($levelType == 0) {
                $type = 'Saga';
            } else if ($levelType == 1) {
                $type = 'Rush';
            } else {
                $type = "EventX";
            }
            $data = DB::connection('mysqluserDB')->table('levels')->select('data')
                ->when($level, function ($query) use ($level) {
                    $query->where('level', $level);
                })
                ->when($sublevel, function ($query) use ($sublevel) {
                    $query->where('sublevel', $sublevel);
                })
                ->when($levelVersion, function ($query) use ($levelVersion) {
                    $query->where('levelVersion', $levelVersion);
                })
                ->when($appVersion, function ($query) use ($appVersion) {
                    $query->where('AppVersion', $appVersion);
                })
                ->when($type, function ($query) use ($type) {
                    $query->where('levelType', $type);
                })->orderBy('levelVersion', 'DESC')
                ->first();
            if ($data) {
                return response()->json($data->data);
            }
            return 'Not found';
        } catch (\Throwable $th) {
            return $th;
        }
    }

    public function getLevels(Request $request)
    {
        try {
            $start = $request->get('startLevel');
            $end = $request->get('endLevel');
            $sublevel = $request->get('sublevel');
            $levelVersion = $request->get('levelVersion');
            $appVersion = $request->get('appVersion');
            $levelType = intval($request->get('levelType'));

            $type = null;
            if ($levelType == 0) {
                $type = 'Saga';
            } else if ($levelType == 1) {
                $type = 'RewardRush/ChallengeRace';
            } else {
                $type = "EventX";
            }

            $data = DB::connection('mysqluserDB')->table('levels')->select('data', 'levelVersion', 'appVersion', 'level', 'sublevel')
                ->when($start && $end, function ($query) use ($start, $end) {
                    $query->whereBetween('level', [$start, $end]);
                })
                ->when($sublevel, function ($query) use ($sublevel) {
                    $query->where('sublevel', $sublevel);
                })
                ->when($levelVersion, function ($query) use ($levelVersion) {
                    if ($levelVersion == -1) {
                        $query->orderBy('levelVersion', 'DESC');
                    } else {
                        $query->where('levelVersion', $levelVersion);
                    }
                })
                ->when($appVersion, function ($query) use ($appVersion) {
                    if ($appVersion == -1) {
                        $query->orderBy('levelVersion', 'DESC');
                    } else {
                        $query->where('appVersion', $appVersion);
                    }
                })
                ->when($type, function ($query) use ($type) {
                    $query->where('levelType', $type);
                })
                ->get();
            if ($data) {
                return response()->json($data);
            }
            return 'Not found';
        } catch (\Throwable $th) {
            return $th;
        }
    }
    // test api
    public function readDataMapApi(Request $request)
    {
        $level = $request->get('level');
        $sublevel = $request->get('sublevel');
        $levelType = intval($request->get('levelType'));
        $start = $request->get('startLevel');
        $end = $request->get('endLevel');

        $type = null;
        if ($levelType === 0) {
            $type = 'Saga';
        } else if ($levelType === 1) {
            $type = 'Rush';
        } else {
            $type = "EventX";
        }
        // return $type;
        $sub = isset($sublevel) ? "AND subLevel = '$sublevel'" : "";

        $levels = [];
        for ($i = $start; $i <= $end; $i++) {
            $sql = "SELECT * FROM levels
                    WHERE `level` = $i
                    AND levelType = '$type'
                    $sub
                    AND levelVersion = (SELECT MAX(levelVersion) FROM levels WHERE `level` = $i AND levelType = '$type' $sub)
                    ";

            $level = DB::connection('mysqluserDB')->select($sql);
            $levels = array_merge($levels, $level);
        }
        $levels = collect($levels)->sortBy('level');
        $array = [];
        foreach ($levels as $key => $level) {
            $data = $this->convertFileLv($level);
            $data['level'] = $level->level;
            $data['sublevel'] = $level->sublevel;
            array_push($array, $data);
        }
        $cl = collect($array);
        $obs = $cl->map(function ($item, $k) {
            $data = [
                'level' => $item['level'],
                'sublevel' => $item['sublevel'],
                'target' => array_search($item['a'], config('entity.targetType')),
                'mapLevel' => $item['g'] . 'x' . $item['h'],
                'countTarget' => $item['b'],
                'move' => $item['c'],
                'hardLevel' => $item['k'] == 0 ? 'No' : 'Yes',
                'version' => $item['v'],
            ];
            $mapCollect = collect($item['i']);

            $map = $mapCollect->map(function ($m) {
                $arrB = explode(',', $m);
                $data = [];
                foreach ($arrB as $key => $value) {
                    $arrM = explode('_', $value);
                    $data = array_merge($data, [$arrM]);
                }
                $col = collect($data);
                $data = $col->map(function ($item) {
                    for ($i = 0; $i < count($item); $i++) {
                        if ($i == 0) {
                            $item[$i] = array_search($item[$i], config('entity.obsType'));
                        }
                        if ($item[0]) {
                            if ($item[0] == 'TrafficCone') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.levels'));
                                }
                                if ($i == 2) {

                                    $item[$i] = array_search($item[$i], config('entity.entityColor'));
                                }
                            } else if ($item[0] == 'Locker') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.levels'));
                                }
                            } else if ($item[0] == 'Bollard') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.bollard'));
                                }
                            } else if ($item[0] == 'Tunnel') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.direction'));
                                }
                                if ($i == 2) {
                                    $item[$i] = false;
                                }
                                if ($i == 3) {
                                    $item[$i] = false;
                                }
                            } else if ($item[0] == 'Barrier') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.direction'));
                                }
                                if ($i == 2) {
                                    $item[$i] = false;
                                }
                                if ($i == 3) {
                                    $item[$i] = false;
                                }
                            } else if ($item[0] == 'Container') {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.entityColor'));
                                }
                            } else {
                                if ($i == 1) {
                                    $item[$i] = array_search($item[$i], config('entity.direction'));
                                }
                                if ($i == 2) {
                                    $item[$i] = array_search($item[$i], config('entity.entityColor'));
                                }

                            }
                        } else {
                            $item[$i] = null;
                        }
                    }
                    return implode('', $item);
                });
                return $data;
            });

            $data['obstacle'] = array_count_values(array_diff(Arr::flatten($map->toArray()), [""]));
            return $data;
        });

        if ($type == "Saga") {
            $sql = "SELECT `Level`, AVG(UsedCoins) AS usedCoinds FROM levelanalytics WHERE levelType = 'Saga' GROUP BY `Level` ORDER BY `Level`";
            $conversionSaga = collect(DB::select($sql))->toArray();
            $playdata = $this->playDataLevelSaga();
            // return $playdata[0]->attempts;
            for ($i = 0; $i < count($obs); $i++) {
                $obs[$i] = array_merge($obs[$i], ["attempts" => $playdata[$i]->attempts, "droprate" => $playdata[$i]->droprate]);
                $obs[$i] = array_merge($obs[$i], ["conversion" => $conversionSaga[$i]->usedCoinds]);
            }
        }

        if ($type == "Rush") {
            $sql = "SELECT `Level`, AVG(UsedCoins) AS usedCoinds FROM levelanalytics WHERE LevelType = 'Rush' AND SubLevel = $sublevel GROUP BY `Level` ORDER BY `Level`";

            $conversionRush = collect(DB::select($sql))->toArray();

            $playdata = $this->playDataLevelRush($sublevel);

            for ($i = 0; $i < count($obs); $i++) {
                $obs[$i] = array_merge($obs[$i], ["attempts" => $playdata[$i]->attempts, "droprate" => $playdata[$i]->droprate]);
                $obs[$i] = array_merge($obs[$i], ["conversion" => $conversionRush[$i]->usedCoinds]);
            }

        }

        return $obs;
    }

    public function trackingDataLevelTest($level, $version, $sublevel)
    {

        $table = "StagingLevelAnalytics";
        $data = [];
        $data = array_merge($data, $this->trackingDataLevelAvg($level, $version, $table, $sublevel));
        $data = array_merge($data, $this->trackingDataLevelCount($level, $version, $table, $sublevel));
        $data = array_merge($data, $this->trackingDataLevelUserPlay($level, $version, $table, $sublevel));
        return $data;
    }

    public function trackingDataLevelReleases($level, $version, $sublevel)
    {
        $table = "LevelAnalytics";
        $data = [];
        $data = array_merge($data, $this->trackingDataLevelAvg($level, $version, $table, $sublevel));
        $data = array_merge($data, $this->trackingDataLevelCount($level, $version, $table, $sublevel));
        $data = array_merge($data, $this->trackingDataLevelUserPlay($level, $version, $table, $sublevel));
        return $data;
    }

    public function trackingDataLevelAvg($level, $version, $table, $sublevel)
    {

        $sub = isset($sublevel) ? "AND Sublevel = $sublevel" : "";
        $sql = "SELECT
                l.`Level` AS `level`	,
                AVG(l.ChangeCars) AS changeCars,AVG(l.StartMoves) AS startMoves,AVG(l.ExtraMoves) AS extraMoves,AVG(l.WinMoves) AS winMoves,
                AVG(l.MergedRainbowBus) AS mergedRainbowBus,AVG(l.GarageRainbowBus) AS garageRainbowBus,AVG(l.GarageTowTruck) AS garageTowTruck,
                AVG(l.GaragePoliceCar) AS garagePoliceCar,AVG(l.BoosterRainbowBus) AS boosterRainbowBus,AVG(l.BoosterTowTruck) AS boosterTowTruck,
                AVG(l.BoosterPoliceCar) AS boosterPoliceCar,AVG(l.BuyMoreMoves) AS buyMoreMoves,AVG(l.UsedCoins) AS usedCoins,AVG(l.Scores) as scores,
                AVG(l.PlayTime) AS playTime
                FROM $table AS l
                WHERE l.`Level` = $level $sub AND `Version` = $version
                GROUP BY l.`Level`";

        $track = DB::select($sql);
        if ($track) {
            return collect($track[0])->toArray();
        } else {
            return [];
        }

    }

    public function trackingDataLevelCount($level, $version, $table, $sublevel)
    {
        $sub = isset($sublevel) ? "AND Sublevel = $sublevel" : "";

        $sql = "SELECT `level`, SUM(retry) AS retry, SUM(hasNextCar) AS hasNextCar, SUM(hasAddMoves) AS hasAddMoves, SUM(hasDoubleMana) AS hasDoubleMana
                FROM(
                SELECT
                `Level` AS `level`, COUNT(UserId) AS retry, 0 AS hasNextCar,  0 AS hasAddMoves, 0 AS hasDoubleMana
                FROM $table AS l
                WHERE `Level` = $level AND `Version` = $version AND l.Retry = 1 $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level`, 0 AS retry, COUNT(l.HasNextCar) AS hasNextCar, 0 AS hasAddMoves, 0 AS hasDoubleMana
                FROM  $table AS l
                WHERE `Level` = $level AND `Version` = $version AND HasNextCar = 1 $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level`, 0 AS retry, 0 AS hasNextCar, COUNT(UserId) AS hasAddMoves, 0 AS hasDoubleMana
                FROM  $table AS l
                WHERE `Level` = $level AND `Version` = $version AND HasAddMoves = 1 $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level`, 0 AS retry, 0 AS hasNextCar, 0 AS hasAddMoves, COUNT(UserId) AS hasDoubleMana
                FROM  $table
                WHERE `Level` = $level AND `Version` = $version AND HasDoubleMana = 1 $sub
                GROUP BY `Level`
                )
                $table
                GROUP BY `Level`";

        $track = DB::select($sql);
        if ($track) {
            return collect($track[0])->toArray();
        } else {
            return [];
        }

    }
    public function trackingDataLevelUserPlay($level, $version, $table, $sublevel)
    {
        $sub = isset($sublevel) ? "AND Sublevel = $sublevel" : "";

        $sql = "SELECT `level`, SUM(winCount) AS winCount, SUM(loseCount) AS loseCount, SUM(replay) AS replayCount, SUM(quit) AS quitCount, SUM(userCount) AS userCount,SUM(playCount)  AS playCount
                FROM(
                SELECT
                `Level` AS `level` ,COUNT(UserId) AS winCount, 0 AS loseCount, 0 AS replay, 0 AS quit, 0 AS userCount, 0 AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version AND EndType = 'Win' $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level` ,0 AS winCount, COUNT(UserId) AS loseCount , 0 AS replay, 0 AS quit, 0 AS userCount, 0 AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version AND EndType = 'Lose' $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level` ,0 AS winCount, 0 AS loseCount , COUNT(UserId) AS replay , 0 AS quit, 0 AS userCount, 0 AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version AND EndType = 'Restart/Replay' $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level` ,0 AS winCount, 0 AS loseCount , 0 AS Replay , COUNT(UserId) AS quit, 0 AS userCount, 0 AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version AND EndType = 'Quit' $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level` ,0 AS winCount, 0 AS loseCount , 0 AS Replay , 0 AS quit , COUNT(DISTINCT UserId) AS userCount, 0 AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version $sub
                GROUP BY `Level`
                UNION ALL
                SELECT
                `Level` AS `level` ,0 AS winCount, 0 AS loseCount , 0 AS Replay , 0 AS quit , 0 AS userCount, COUNT(UserId) AS playCount
                FROM $table
                WHERE `Level` = $level AND `Version` = $version $sub
                GROUP BY `Level`
                )
                $table
                GROUP BY `level`";

        $track = DB::select($sql);
        if ($track) {
            return collect($track[0])->toArray();
        } else {
            return [];
        }

    }

    public function playDataLevelSaga()
    {
        $sql = "WITH wintable AS (
                SELECT `Level`,`UserId` FROM  winanalytics
                ), loseTable AS (SELECT `Level`,`UserId` FROM loseanalytics)
                SELECT `Level`,SUM(userCount) AS userCount, SUM(playtime) AS playtime, SUM(playtime)/SUM(userCount) as attempts
                FROM (
                SELECT `Level`, COUNT(DISTINCT UserId) AS userCount, 0 AS playtime
                FROM (
                SELECT UserId,`Level`
                FROM wintable
                GROUP BY `Level`, `UserId`
                UNION ALL
                SELECT UserId,`Level`
                FROM loseTable
                GROUP BY `Level`, `UserId`
                ) a
                GROUP BY `Level`
                UNION ALL
                SELECT `Level`, 0 AS userCount, SUM(playCount) AS playtime
                FROM (
                SELECT `Level`,COUNT(UserId) AS playCount FROM wintable GROUP BY `Level`
                UNION
                SELECT `Level`,COUNT(UserId) AS playCount FROM loseTable GROUP BY `Level`
                ) t GROUP BY `Level` ORDER BY `Level`
                ) tb
                GROUP BY `Level`
                ORDER BY `Level`";

        $data = collect(DB::select($sql))->toArray();

        for ($i = 0; $i < count($data); $i++) {
            if (($i + 1) < count($data)) {
                $data[$i]->droprate = (1 - $data[$i + 1]->userCount / $data[$i]->userCount) * 100;
            } else {
                $data[$i]->droprate = 0;
            }
        }
        return $data;
    }

    public function playDataLevelRush($sublevel)
    {

        $sublevel = $sublevel != null ? "AND `SubLevel` = $sublevel" : "";
        $sql = "SELECT `Level`,`SubLevel`,COUNT(DISTINCT UserId) AS userCount, COUNT(UserId)/COUNT(DISTINCT UserId) AS attempts
                FROM playrushanalytics WHERE SubLevel != 0 $sublevel
                GROUP BY `SubLevel`,`Level` ORDER BY `SubLevel`,`Level`";

        $data = collect(DB::select($sql))->toArray();

        for ($i = 0; $i < count($data); $i++) {
            if (($i + 1) < count($data)) {
                $data[$i]->droprate = (1 - $data[$i + 1]->userCount / $data[$i]->userCount) * 100;
            } else {
                $data[$i]->droprate = 0;
            }
        }
        return $data;
    }
}
