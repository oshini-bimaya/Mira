<?php
/* Shared public-gallery helpers. Only approved artworks can be returned. */
function miraEsc($value): string { return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function miraArtworkImage($path): string {
    $path = str_replace('\\', '/', (string)$path);
    // Uploads from the existing workflow always use this directory.
    if (!preg_match('~^uploads/artworks/[a-zA-Z0-9_-]+\.(?:jpg|jpeg|png|webp)$~i', $path)) return '';
    return $path;
}
function miraApprovedArtworks(mysqli $conn, string $category='', int $id=0, int $limit=100): array {
    $limit=max(1,min(200,$limit));
    $sql="SELECT a.artwork_id,a.title,a.description,a.created_at,a.is_featured,
                 u.FULL_NAME AS artist_name,c.category_name,c.category_id,
                 (SELECT ai.image_path FROM artwork_images ai WHERE ai.artwork_id=a.artwork_id
                  ORDER BY ai.image_id ASC LIMIT 1) AS image_path
          FROM artworks a
          INNER JOIN users u ON u.USER_ID=a.user_id
          INNER JOIN categories c ON c.category_id=a.category_id
          WHERE LOWER(a.status)='approved'";
    if ($id>0) { $sql.=' AND a.artwork_id=?'; $stmt=$conn->prepare($sql.' LIMIT 1'); $stmt->bind_param('i',$id); }
    elseif ($category!=='') { $sql.=' AND c.category_name=?'; $stmt=$conn->prepare($sql.' ORDER BY a.is_featured DESC,a.created_at DESC,a.artwork_id DESC LIMIT '.$limit);$stmt->bind_param('s',$category); }
    else { $stmt=$conn->prepare($sql.' ORDER BY a.is_featured DESC,a.created_at DESC,a.artwork_id DESC LIMIT '.$limit); }
    $stmt->execute(); $result=$stmt->get_result(); $rows=[];
    while($row=$result->fetch_assoc()) {
        // Do not publicly expose artworks with missing or invalid image paths.
        if(miraArtworkImage($row['image_path'])!=='') $rows[]=$row;
    }
    $stmt->close();return $rows;
}
