<?php

namespace App\Providers;

use App\Providers\BaseServiceProvider;
use App\Models\User;
use App\Models\Bracket;
use App\Models\Subscription;
use Excel;

/**
 * HomeServiceProvider class contains methods for dashboard management
 */
class HomeServiceProvider extends BaseServiceProvider {

    /**
     * Dater analytics to be shown in dashboard.
     * @return array
     */
    public static function getDashboardDetails($request = NULL) {
        $gender_analytics = User::genderAnalytics($request);
        $age_analytics = User::ageAnalytics($request);
        $occupation_analytics = User::occupationAnalytics($request);
        $bracket_analytics = Bracket::bracketAnalytics($request);
        $dashboard_data = ['gender_analytics' => $gender_analytics, 'age_analytics' => $age_analytics, 'occupation_analytics' => $occupation_analytics, 'bracket_analytics' => $bracket_analytics];

        return $dashboard_data;
    }

    /**
     * Download analytics in Excel format
     * @param array $data
     * @param string $file_name
     * @return excel
     */
    public static function downloadExcel($data, $file_name) {
        $gender_analytics = $data['gender_analytics'];
        $age_analytics = $data['age_analytics'];
        $occupation_analytics = $data['occupation_analytics'];
        $bracket_analytics = $data['bracket_analytics'];

        $gender_analytics_arr[0] = Array();
        $age_analytics_arr[0] = Array();
        $occupation_analytics_arr[0] = Array();
        $bracket_analytics_arr[0] = Array();

        foreach ($gender_analytics as $key => $value) {
            $gender_analytics_arr[0][$key] = $value;
        }

        foreach ($age_analytics as $key => $value) {
            $age_analytics_arr[0][$key] = $value;
        }

        foreach ($occupation_analytics as $key => $value) {
            $occupation_analytics_arr[0][$key] = $value;
        }

        foreach ($bracket_analytics as $key => $value) {
            $bracket_analytics_arr[0]['No. of ' . $key] = $value;
        }

        return Excel::create($file_name, function($excel) use ($gender_analytics_arr, $age_analytics_arr, $occupation_analytics_arr, $bracket_analytics_arr) {
                    $excel->sheet('Gender Analytics', function($sheet) use ($gender_analytics_arr) {
                        $data_header = array('Gender', 'No. of Daters');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($gender_analytics_arr[0] as $key => $value) {
                            $data_values = array(ucfirst($key), $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                    $excel->sheet('Age Analytics', function($sheet) use ($age_analytics_arr) {
                        $data_header = array('Age Group', 'No. of Daters');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($age_analytics_arr[0] as $key => $value) {
                            $data_values = array($key, $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                    $excel->sheet('Occupation Analytics', function($sheet) use ($occupation_analytics_arr) {
                        $data_header = array('Occupation', 'No. of Daters');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($occupation_analytics_arr[0] as $key => $value) {
                            $data_values = array($key, $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                    $excel->sheet('Bracket Analytics', function($sheet) use ($bracket_analytics_arr) {
                        $data_header = array('Bracket Analytics', 'Count');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($bracket_analytics_arr[0] as $key => $value) {
                            $data_values = array($key, $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                })->export('xls');
    }

    /**
     * Inapp analytics to be shown in dashboard.
     * @return array
     */
    public static function getInAppDetails($request = NULL) {
        $in_app_analytics = Subscription::inAppAnalytics($request);
        $amount_analytics = Subscription::amountAnalytics($request);
        $in_app_data = ['in_app_analytics' => $in_app_analytics, 'amount_analytics' => $amount_analytics];
        return $in_app_data;
    }

    /**
     * Download in app analytics in Excel format
     * @param array $data
     * @param string $file_name
     * @return excel
     */
    public static function inAppDownloadExcel($data, $file_name) {
        $in_app_analytics = $data['in_app_analytics'];
        $amount_analytics = $data['amount_analytics'];

        $in_app_analytics_arr[0] = Array();
        $amount_analytics_arr[0] = Array();

        foreach ($in_app_analytics as $key => $value) {
            $in_app_analytics_arr[0][$key] = $value;
        }

        foreach ($amount_analytics as $key => $value) {
            $amount_analytics_arr[0][$key] = $value;
        }

        return Excel::create($file_name, function($excel) use ($in_app_analytics_arr, $amount_analytics_arr) {
                    $excel->sheet('In App Analytics', function($sheet) use ($in_app_analytics_arr) {
                        $data_header = array('In-App Purchase', 'No. of New Daters');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($in_app_analytics_arr[0] as $key => $value) {
                            $data_values = array(ucfirst($key), $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                    $excel->sheet('Amount Analytics', function($sheet) use ($amount_analytics_arr) {
                        $data_header = array('In-App Purchase', 'Total Amount Recieved');
                        $sheet->fromArray(array($data_header), null, 'A1', false, false);

                        foreach ($amount_analytics_arr[0] as $key => $value) {
                            $data_values = array($key, '$' . $value);
                            $sheet->fromArray(array($data_values), null, 'A1', false, false);
                        }
                        $sheet->cells('A1:B1', function($cells) {
                            $cells->setFontWeight('bold');
                            $cells->setFontSize(12);
                        });
                    });
                })->export('xls');
    }

}
