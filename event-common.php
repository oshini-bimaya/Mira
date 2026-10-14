<?php
// Shared helpers; require after session_start and db.php.
function eventEsc($value): string {return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');}
function eventToken(): string {if(empty($_SESSION['event_csrf'])) $_SESSION['event_csrf']=bin2hex(random_bytes(32));return $_SESSION['event_csrf'];}
function eventValidToken(): bool {return isset($_POST['csrf'],$_SESSION['event_csrf']) && hash_equals($_SESSION['event_csrf'],(string)$_POST['csrf']);}
function eventStudent(mysqli $conn, int $uid): bool {$s=$conn->prepare('SELECT ROLE FROM users WHERE USER_ID=?');$s->bind_param('i',$uid);$s->execute();$r=$s->get_result()->fetch_assoc();$s->close();return $r && strtoupper($r['ROLE'])==='STUDENT';}
function eventGo(string $location): void {header('Location: '.$location);exit;}
