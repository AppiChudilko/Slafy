<?php

namespace Server;

if (!defined('AppiEngine')) {
    header( "refresh:0; url=/");
}

/**
 * News
 */
class Files
{
    /**
     * @param $groupId
     * @return bool
     */
    public function uploadGroupAvatar($groupId) {

        global $qb;
        global $user;

        if (!$user->isGroupOwner($groupId))
            return false;

        $group = new Group();
        $groupInfo = $group->getGroupById($groupId);

        if($groupInfo['avatar'] != '/client/images/none-ava.png' && $groupInfo['avatar'] != 'https://www.inkmt.com/img/default-sq.jpg')
            $this->deleteFile(str_replace('https://byappi.com/', '', $groupInfo['avatar']));

        $postfixArray = explode('_', $groupInfo['avatar']);
        $postfix = (empty(end($postfixArray))) ? 0 : end($postfixArray);
        $postfix = intval($postfix);
        $path = '/upload/group/';

        $files = $this->uploadImage(md5('av' . $groupInfo['id'] . time()), $path . $groupInfo['id'] . '/', $postfix);
        $success = false;

        if(isset($files['files']) && !empty($files['files']))
            $success = $qb
                ->createQueryBuilder('groups')
                ->updatesql(
                    [
                        'avatar'
                    ], [
                        'https://byappi.com/' . $path . $groupInfo['id'] . '/' . reset($files['files'])
                    ]
                )
                ->where('id = \'' . $groupInfo['id'] . '\'')
                ->executeQuery()
                ->getResult()
            ;
        else
            return false;

        $this->deleteFile($path . reset($files['files']));
        return $success;
    }

    /**
     * @return string
     */
    public function uploadImgurImg() {

        $client_id = '3d77b75a2bb445e';
        $fileItem = reset($_FILES);
        $file = file_get_contents($fileItem['tmp_name']);

        $url = 'https://api.imgur.com/3/image.json';
        $headers = array("Authorization: Client-ID $client_id");
        $pvars  = array('image' => base64_encode($file));

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL=> $url,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => 1,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => $pvars
        ));

        $json_returned = curl_exec($curl); // blank response
        curl_close ($curl);
        $this->deleteFile($fileItem['tmp_name']);
        print_r($json_returned);
        //$this->deleteImgurImg();
        return $json_returned;
    }

    /**
     * @return string
     */
    public function deleteImgurImg($imgId) {

        $client_id = "3d77b75a2bb445e";
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.imgur.com/3/image/".$imgId);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

        $headers = array();
        $headers[] = "Authorization: Client-ID ".$client_id;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
        //var_dump($result);
    }

    /**
     * @return array
     */
    public function uploadCourseFile() {

        global $userInfo;
        global $qb;

        $path = '/upload/course/' . $userInfo['id'] . '_' . hash('sha256', time() . '*' . $userInfo['id']) . '/';

        $files = $this->uploadFile(md5('file' . $userInfo['id'] . time()), $path);
        $success = false;

        if(isset($files['files'])) {
            $course = $qb
                ->createQueryBuilder('course')
                ->selectSql()
                ->where('owner_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;

            $format = $this->getFileFormat(reset($files['files']));

            $formatType = 0;

            $formatArrayWord = [
                'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm'
            ];
            $formatArrayExcel = [
                'xls', 'xlsx', 'xlsm', 'xlt', 'xltm', 'xltx', 'xltb', 'xla', 'xlam'
            ];
            $formatArrayPowerPoint = [
                'ptt', 'pttx', 'pttm', 'pps', 'ppsx', 'ppsm', 'pot', 'potx', 'potm', 'ppa', 'ppam'
            ];
            $formatArrayPdf = [
                'pdf'
            ];

            if(in_array($format, $formatArrayWord))
                $formatType = 1;
            else if(in_array($format, $formatArrayExcel))
                $formatType = 2;
            else if(in_array($format, $formatArrayPowerPoint))
                $formatType = 3;
            else if(in_array($format, $formatArrayPdf))
                $formatType = 4;

            if (!empty($course))
                $success = $qb
                    ->createQueryBuilder('course_files')
                    ->insertSql(
                        [
                            'owner_id',
                            'course_id',
                            'title',
                            'path',
                            'type',
                            'timestamp',
                        ], [
                            $userInfo['id'],
                            $course['id'],
                            reset($files['files']),
                            $path . reset($files['files']),
                            $formatType,
                            time(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
        }
        else
            return $files;

        if($success) {
            $file = $qb
                ->createQueryBuilder('course_files')
                ->selectSql()
                ->where('owner_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;

            if (!empty($file))
                return ['success' => ['message' => 'Файл был загружен', 'id' => $file['id']]];
        }

        $this->deleteFile($path . reset($files['files']));
        return ['error' => 'Ошибка загрузки файла'];
    }

    /**
     * @return array
     */
    public function uploadCourseVideo() {

        global $userInfo;
        global $qb;

        $path = '/upload/course/video/' . $userInfo['id'] . '_' . hash('sha256', time() . '*' . $userInfo['id']) . '/';

        $files = $this->uploadVideo(md5('video' . $userInfo['id'] . time()), $path);
        $success = false;

        if(isset($files['files'])) {
            $course = $qb
                ->createQueryBuilder('course')
                ->selectSql()
                ->where('owner_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;

            if (!empty($course))
                $success = $qb
                    ->createQueryBuilder('course_videos')
                    ->insertSql(
                        [
                            'owner_id',
                            'course_id',
                            'title',
                            'path',
                            'timestamp',
                        ], [
                            $userInfo['id'],
                            $course['id'],
                            reset($files['files']),
                            $path . reset($files['files']),
                            time(),
                        ]
                    )
                    ->executeQuery()
                    ->getResult()
                ;
        }
        else
            return $files;

        if($success) {
            $file = $qb
                ->createQueryBuilder('course_videos')
                ->selectSql()
                ->where('owner_id = \'' . $userInfo['id'] . '\'')
                ->limit(1)
                ->orderBy('id DESC')
                ->executeQuery()
                ->getSingleResult()
            ;

            if (!empty($file))
                return ['success' => ['message' => 'Видео было загружено', 'id' => $file['id']]];
        }

        $this->deleteFile($path . reset($files['files']));
        return ['error' => 'Ошибка загрузки видео'];
    }

    /**
     * @return array
     */
    public function uploadImageFeed() {

        global $userInfo;
        global $user;

        $compress = 60;
        if (!$user->isLogin())
            return ['error' => 'Аккаунт не авторизован'];

        if ($user->isSubscribe()) { //TODO доделать проверку на GIF
            $compress = 0;
        }

        $fileName = hash('sha256', $userInfo['login'] . time()) . rand(0, PHP_INT_MAX);

        $path = '/upload/feed/' . $userInfo['id'];
        $files = $this->uploadImage($fileName, $path . '/', $compress);
        if(!isset($files['error']))
            return ['success' => array_merge(['message' => 'Изображение было загружено'], $files)];
        //$this->deleteFile($path . reset($files['files']));
        return $files;
    }

    /**
     * @return array
     */
    public function uploadImageApartment() {

        global $userInfo;
        global $user;

        $compress = 60;
        if (!$user->isLogin())
            return ['error' => 'Аккаунт не авторизован'];

        $fileName = hash('sha256', $userInfo['login'] . time()) . rand(0, PHP_INT_MAX);

        $path = '/upload/apartment/' . $userInfo['id'];
        $files = $this->uploadImage($fileName, $path . '/', $compress);
        if(!isset($files['error']))
            return ['success' => array_merge(['message' => 'Изображение было загружено'], $files)];
        //$this->deleteFile($path . reset($files['files']));
        return $files;
    }

    /**
     * @return array
     */
    public function uploadImageOther() {

        global $userInfo;
        global $user;

        $compress = 60;
        if (!$user->isLogin())
            return ['error' => 'Аккаунт не авторизован'];

        $fileName = hash('sha256', $userInfo['login'] . time()) . rand(0, PHP_INT_MAX);

        $path = '/upload/other/' . $userInfo['id'];
        $files = $this->uploadImage($fileName, $path . '/', $compress);
        if(!isset($files['error']))
            return ['success' => array_merge(['message' => 'Изображение было загружено'], $files)];
        //$this->deleteFile($path . reset($files['files']));
        return $files;
    }

    /**
     * @return array
     */
    public function uploadUserAvatar() {

        global $userInfo;
        global $user;

        $compress = 60;
        if (!$user->isLogin())
            return ['error' => 'Аккаунт не авторизован'];

        $fileName = hash('sha256', $userInfo['login'] . time()) . rand(0, PHP_INT_MAX);
        $path = '/upload/user/' . $userInfo['id'];
        if ($user->isSubscribe()) { //TODO доделать проверку на GIF
            $compress = 0;
        }

        $files = $this->uploadImage($fileName, $path . '/', $compress, 25);
        if(!isset($files['error']))
            return ['success' => array_merge(['message' => 'Изображение было загружено'], $files)];
        //$this->deleteFile($path . reset($files['files']));
        return $files;
    }

    /**
     * @return array
     */
    public function uploadImageStory() {

        global $userInfo;
        global $user;

        $compress = 60;
        if (!$user->isLogin())
            return ['error' => 'Аккаунт не авторизован'];

        if ($user->isSubscribe())
            $compress = 0;

        $fileName = hash('sha256', $userInfo['login'] . time()) . rand(0, PHP_INT_MAX);

        $path = '/upload/story/' . $userInfo['id'];
        $files = $this->uploadImage($fileName, $path . '/', $compress);
        if(!isset($files['error']))
            return ['success' => array_merge(['message' => 'Изображение было загружено'], $files)];
        $this->deleteFile($path . reset($files['files']));
        return $files;
    }

    /**
     * @return array
     */
    public function uploadImageTexture() {
        $path = '/upload/iphone/texture';
        $files = $this->uploadImage(md5(time()), $path . '/', 0);
        $success = true;

        if($success)
            return ['success' => ['message' => 'Изображение было загружено', 'files' => $files]];

        $this->deleteFile($path . reset($files['files']));
        return ['error' => 'Ошибка загрузки изображения'];
    }

    /**
     * @return array
     */
    public function uploadImageExclusive() {
        $path = '/upload/iphone/exclusive';
        $name = md5(time());
        $files = $this->uploadImage($name, $path . '/', 0);
        $success = true;

        if($success)
            return ['success' => ['message' => 'Изображение было загружено', 'files' => $files, 'hash' => $name]];

        $this->deleteFile($path . reset($files['files']));
        return ['error' => 'Ошибка загрузки изображения'];
    }

    /**
     * @param $fileName
     * @param $newFileName
     * @return array
     */
    public function switchImageBlogTempToNews($fileName, $newFileName) {

        $fileFormat = $this->getFileFormat($fileName);
        $path = '../../byappi.com/public_html/upload/news/temp/';
        $newPath = '../../byappi.com/public_html/upload/news/';

        return rename($path . $fileName, $newPath . $newFileName . '.' . $fileFormat);

        /*if(rename($path . $fileName, $newPath . $newFileName . $fileFormat))
            return ['success' => ['message' => 'Изображение было загружено', 'files' => $files]];

        $this->deleteFile($path . reset($files['files']));
        return ['error' => 'Ошибка загрузки фона'];*/
    }

    /**
     * @param string $imageName
     * @param string $uploadDir
     * @param int $postfix
     * @param int $size
     * @return array
     * @internal param string $successText
     */
    public function uploadImage($imageName, $uploadDir = '/upload/', $compress = 60, $size = 20) {

        $error = false;
        $files = [];

        $uploadDir = $_SERVER['DOCUMENT_ROOT'] . $uploadDir;

        $imagesArray = ['jpg', 'png', 'gif', 'jpeg'];

        if(!file_exists($uploadDir))
            mkdir($uploadDir, 0777, true);

        /*
        $image = imagecreatefromgif($path_to_gif_image); GIF TO JPEG
        imagejpeg($image, $output_path_with_jpg_extension);
         * */

        $file = reset($_FILES);

        if (is_array($file['name'])) {
            for ($i = 0; $i < count($file['name']); $i++) {

                $fileFormat = $this->getFileFormat($file['name'][$i]);

                if($file["size"][$i] > $size * 1024 * 1024) {
                    $this->deleteFile($file['tmp_name'][$i]);
                    return ['error' => 'Файл слишком велик'];
                }

                if(!in_array($fileFormat, $imagesArray)) {
                    $this->deleteFile($file['tmp_name'][$i]);
                    return ['error' => 'Картинка должна быть GIF / PNG / JPG!'];
                }

                $this->compressImage($file['tmp_name'][$i], $uploadDir . basename($file['name'][$i]), $compress);
                $imageNameDone = $imageName . $i . '.' . str_replace('image/', '', $file['type'][$i]);
                rename($uploadDir . $file['name'][$i], $uploadDir . $imageNameDone);
                $files[] = $imageNameDone;
            }
        }
        else {
            $fileFormat = $this->getFileFormat($file['name']);
            if($file["size"] > $size * 1024 * 1024) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Файл слишком велик'];
            }

            if(!in_array($fileFormat, $imagesArray)) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Картинка должна быть GIF / PNG / JPG!'];
            }

            $this->compressImage($file['tmp_name'], $uploadDir . basename($file['name']), $compress);
            $imageNameDone = $imageName . '.' . str_replace('image/', '', $file['type']);
            rename($uploadDir . $file['name'], $uploadDir . $imageNameDone);
            if ($fileFormat === 'gif')
                $this->compressImage($uploadDir . $imageNameDone, $uploadDir . $imageName . '.jpg', 99);
            $files[] = $imageNameDone;
        }

        return $error ? ['error' => 'Ошибка загрузки изображения'] : ['files' => $files];
    }

    /**
     * @param string $source
     * @param string $destination
     * @param int $quality
     */
    public function compressImage($source, $destination, $quality = 0, $resize = 0) { //TODO

        $info = getimagesize($source);
        $exif = @exif_read_data($source);

        if ($info['mime'] == 'image/gif' && $quality == 0) {
            move_uploaded_file($source, $destination);
            return;
        }

        if (empty($exif)) {

            $image = match ($info['mime']) {
                'image/jpg' => imagecreatefromjpeg($source),
                'image/jpeg' => imagecreatefromjpeg($source),
                'image/png' => imagecreatefrompng($source),
                'image/gif' => imagecreatefromgif($source),
                'image/x-png' => imagecreatefrompng($source),
                'image/webp' => imagecreatefromwebp($source),
                default => imagecreatefromjpeg($source),
            };
            if ($quality)
                imagejpeg($image, $destination, $quality);
            else
                imagejpeg($image, $destination);
            imagedestroy($image);
            return;
        }

        if ($info['mime'] == 'image/jpg' || $info['mime'] == 'image/jpeg') {
            $imageResource = imagecreatefromjpeg($source);
            $image = match ($exif['Orientation']) {
                3 => imagerotate($imageResource, 180, 0),
                6 => imagerotate($imageResource, -90, 0),
                8 => imagerotate($imageResource, 90, 0),
                default => $imageResource,
            };
            if ($quality)
                imagejpeg($image, $destination, $quality);
            else
                imagejpeg($image, $destination);
            imagedestroy($image);
        }
        else if ($info['mime'] == 'image/webp') {
            $imageResource = imagecreatefromwebp($source);
            $image = match ($exif['Orientation']) {
                3 => imagerotate($imageResource, 180, 0),
                6 => imagerotate($imageResource, -90, 0),
                8 => imagerotate($imageResource, 90, 0),
                default => $imageResource,
            };
            if ($quality)
                imagejpeg($image, $destination, $quality);
            else
                imagejpeg($image, $destination);
            imagedestroy($image);
        }
        elseif ($info['mime'] == 'image/gif') {
            if ($quality == 0)
                move_uploaded_file($source, $destination);
            else {
                $imageResource = imagecreatefromgif($source);
                $image = match ($exif['Orientation']) {
                    3 => imagerotate($imageResource, 180, 0),
                    6 => imagerotate($imageResource, -90, 0),
                    8 => imagerotate($imageResource, 90, 0),
                    default => $imageResource,
                };
                if ($quality)
                    imagejpeg($image, $destination, $quality);
                else
                    imagejpeg($image, $destination);
                imagedestroy($image);
            }
            /*$imageResource = imagecreatefromgif($source);
            $image = match ($exif['Orientation']) {
                3 => imagerotate($imageResource, 180, 0),
                6 => imagerotate($imageResource, -90, 0),
                8 => imagerotate($imageResource, 90, 0),
                default => $imageResource,
            };
            imagegif($image, $destination);
            imagedestroy($image);*/
        }
        elseif ($info['mime'] == 'image/png' || $info['mime'] == 'image/x-png') {
            $imageResource = imagecreatefrompng($source);
            $image = match ($exif['Orientation']) {
                3 => imagerotate($imageResource, 180, 0),
                6 => imagerotate($imageResource, -90, 0),
                8 => imagerotate($imageResource, 90, 0),
                default => $imageResource,
            };
            if ($quality)
                imagepng($image, $destination, $quality);
            else
                imagepng($image, $destination);
            imagedestroy($image);
        }

    }

    /**
     * @param string $fileName
     * @param string $uploadDir
     * @param int $size
     * @return array
     * @internal param string $successText
     */
    public function uploadFile($fileName, $uploadDir = '/upload/course/', $size = 2) {

        $error = false;
        $files = [];

        $uploadDir = $uploadDir;

        $filesArray = [
            'doc', 'docx', 'docm', 'dot', 'dotx', 'dotm',
            'xls', 'xlsx', 'xlsm', 'xlt', 'xltm', 'xltx', 'xltb', 'xla', 'xlam',
            'ptt', 'pttx', 'pttm', 'pps', 'ppsx', 'ppsm', 'pot', 'potx', 'potm', 'ppa', 'ppam',
            'pdf', 'txt'
        ];

        if(!file_exists($uploadDir))
            mkdir($uploadDir, 0777 , true);

        foreach($_FILES as $file) {

            $fileFormat = $this->getFileFormat($file['name']);

            if($file["size"] > $size * 1024 * 1024) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Файл слишком велик'];
            }

            if(!in_array($fileFormat, $filesArray)) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Должен быть TXT / PDF / Word / Excel / PowerPoint документ!'];
            }

            if( move_uploaded_file( $file['tmp_name'], $uploadDir . basename($file['name']) ) ) {
                $fileName = $fileName . '.' . $fileFormat;
                rename($uploadDir . $file['name'], $uploadDir . $fileName);
                $files[] = $fileName;
            }
            else
                $error = true;
        }

        return $error ? ['error' => 'Ошибка загрузки файлов.'] : ['files' => $files];
    }

    /**
     * @param string $fileName
     * @param string $uploadDir
     * @param int $size
     * @return array
     * @internal param string $successText
     */
    public function uploadVideo($fileName, $uploadDir = '/upload/course/video/', $size = 16) {

        $error = false;
        $files = [];

        $uploadDir = $uploadDir;

        $filesArray = [
            'mp4', 'avi', 'wmv', 'mov', 'mpeg'
        ];

        if(!file_exists($uploadDir))
            mkdir($uploadDir, 0777 , true);

        foreach($_FILES as $file) {

            $fileFormat = $this->getFileFormat($file['name']);

            if($file["size"] > $size * 1024 * 1024) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Файл слишком велик'];
            }

            if(!in_array($fileFormat, $filesArray)) {
                $this->deleteFile($file['tmp_name']);
                return ['error' => 'Должен быть MP4 / AVI / WMW / MOV / MPEG!'];
            }

            if( move_uploaded_file( $file['tmp_name'], $uploadDir . basename($file['name']) ) ) {
                $fileName = $fileName . '.' . $fileFormat;
                rename($uploadDir . $file['name'], $uploadDir . $fileName);
                $files[] = $fileName;
            }
            else
                $error = true;
        }

        return $error ? ['error' => 'Ошибка загрузки файлов.'] : ['files' => $files];
    }

    /**
     * @param string $path
     * @return bool
     */
    public function deleteFile($path) {
        if (file_exists('../../adaptation-usa.com/public_html/' . $path))
            return unlink('../../adaptation-usa.com/public_html/' . $path);
        return false;
    }

    /**
     * @param string $path
     * @return string
     */
    public function uploadExternalFile($path, $url, $isVideo = false) {
        if(!file_exists($path))
            mkdir($path, 0777 , true);
        $image = file_get_contents($url);
        $fileName = $this->generateFileName();
        if ($isVideo) {
            file_put_contents($path . $fileName . '.mp4', $image);
            return $fileName . '.mp4';
        }
        file_put_contents($path . $fileName . '.jpg', $image);
        return $fileName . '.jpg';
    }

    /**
     * @param string $path
     * @return bool
     */
    public function generateFileName($id = 0) {
        return hash('sha256', $id . time()) . rand(0, PHP_INT_MAX);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFileFormat($fileName) {
        $img = explode('.', $fileName);
        return end($img);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFileNameWithoutFormat($fileName) {
        $img = explode('.', $fileName);
        return reset($img);
    }
}