<?php
require __DIR__ . '/auth.php';
// Admin-only read-only CSV exports. Queries are constants chosen from a strict allowlist.
$exports = [
 'artworks'=>['filename'=>'mira_artworks.csv', 'headers'=>['ID','Title','Artist','Category','Status','Featured','Submitted'], 'sql'=>"SELECT a.artwork_id,a.title,u.FULL_NAME,c.category_name,a.status,a.is_featured,a.created_at FROM artworks a JOIN users u ON u.USER_ID=a.user_id LEFT JOIN categories c ON c.category_id=a.category_id ORDER BY a.created_at DESC"],
 'students'=>['filename'=>'mira_students.csv', 'headers'=>['ID','Name','Email','Account status','Registered','Artworks'], 'sql'=>"SELECT u.USER_ID,u.FULL_NAME,u.EMAIL,u.account_status,u.CREATED_AT,(SELECT COUNT(*) FROM artworks a WHERE a.user_id=u.USER_ID) FROM users u WHERE UPPER(u.ROLE)='STUDENT' ORDER BY u.CREATED_AT DESC"],
 'exhibitions'=>['filename'=>'mira_exhibitions.csv', 'headers'=>['ID','Title','Venue','Starts','Ends','Status','Registrations','Artworks'], 'sql'=>"SELECT e.event_id,e.title,e.venue,e.starts_at,e.ends_at,e.status,(SELECT COUNT(*) FROM event_registrations r WHERE r.event_id=e.event_id AND r.status IN ('registered','attended')),(SELECT COUNT(*) FROM event_artworks a WHERE a.event_id=e.event_id) FROM events e ORDER BY e.starts_at DESC"],
 'requests'=>['filename'=>'mira_exhibition_requests.csv', 'headers'=>['ID','Exhibition','Artwork','Student','Status','Requested','Reviewed','Admin note'], 'sql'=>"SELECT r.request_id,e.title,a.title,u.FULL_NAME,r.status,r.requested_at,r.reviewed_at,r.admin_note FROM exhibition_requests r JOIN events e ON e.event_id=r.event_id JOIN artworks a ON a.artwork_id=r.artwork_id JOIN users u ON u.USER_ID=r.user_id ORDER BY r.requested_at DESC"],
];
$type=(string)($_GET['type']??'');
if(!isset($exports[$type])) {http_response_code(400);exit('Invalid report type.');}
$report=$exports[$type];
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$report['filename'].'"');
header('Cache-Control: no-store, private');
$out=fopen('php://output','w');
// UTF-8 BOM enables readable Unicode characters in Excel.
fwrite($out,"\xEF\xBB\xBF");
fputcsv($out,$report['headers']);
$result=$conn->query($report['sql']);
while($row=$result->fetch_row()) {
    $safe=array_map(static function ($value): string {
        $value=(string)($value??'');
        // Prevent CSV spreadsheet formula injection; keep original data in DB.
        if(preg_match('/^[\x00-\x20]*[=+@\-]/u',$value)) $value="'".$value;
        return $value;
    },$row);
    fputcsv($out,$safe);
}
fclose($out);
exit;
