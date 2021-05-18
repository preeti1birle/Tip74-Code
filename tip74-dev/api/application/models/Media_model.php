<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

// include("./vendor/autoload.php");
// use Aws\S3\S3Client;

class Media_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        // $credentials = new Aws\Credentials\Credentials(S3_KEY, S3_ACCESS_KEY);
        // $this->S3 = new Aws\S3\S3Client([
        //     'version'     => S3_VERSION,
        //     'region'      => S3_REGION,
        //     'credentials' => $credentials
        // ]);
    }

    function getMedia($Field, $Where = array(), $multiRecords = FALSE, $PageNo = 1, $PageSize = 15) {
        $Return = array('Data' => array('Records' => array()));
        $this->db->select($Field);
        $this->db->from('tbl_media M');
        $this->db->from('tbl_entity E');
        $this->db->where("M.MediaID", "E.EntityID", FALSE);
        $this->db->from('tbl_media_sections MS');
        $this->db->where("M.SectionID", "MS.SectionID", FALSE);
        if (!empty($Where['SectionID'])) {
            $this->db->where("M.SectionID", $Where['SectionID']);
        } if (!empty($Where['MediaID'])) {
            $this->db->where("M.MediaID", $Where['MediaID']);
        } if (!empty($Where['EntityID'])) {
            $this->db->where("M.EntityID", $Where['EntityID']);
        } if (!empty($Where['MediaGUID'])) {
            $this->db->where("M.MediaGUID", $Where['MediaGUID']);
        }
        if (!empty($Where['OrderBy']) && !empty($Where['Sequence'])) {
            $this->db->order_by($Where['OrderBy'], $Where['Sequence']);
        } else {
            $this->db->order_by('M.MediaID', 'DESC');
        }
        if ($multiRecords) {
            $TempOBJ = clone $this->db;
            $TempQ = $TempOBJ->get();
            $Return['Data']['TotalRecords'] = $TempQ->num_rows();
            $this->db->limit($PageSize, paginationOffset($PageNo, $PageSize));
        } else {
            $this->db->limit(1);
        } $Query = $this->db->get();
        if ($Query->num_rows() > 0) {
            if ($multiRecords) {
                $Return['Data']['Records'] = $Query->result_array();
                return $Return;
            } else {
                return $Query->row_array();
            }
        } return FALSE;
    }

    function addMedia($UserID, $SectionID, $Input = array()) {
        $this->db->trans_start();
        $EntityGUID = (!empty($Input['EntityGUID']) ? $Input['EntityGUID'] : get_guid());
        $EntityID = $this->Entity_model->addEntity($EntityGUID, array("EntityTypeID" => 2, "UserID" => $UserID, "StatusID" => 2));
        $InsertData = array_filter(array("MediaID" => $EntityID, "MediaGUID" => $EntityGUID, "IsImage" => $Input['IsImage'], "UserID" => $UserID, "SectionID" => $SectionID, "MediaRealName" => $Input['MediaRealName'], "MediaName" => $Input['MediaName'], "MediaSize" => $Input['MediaSize'], "MediaExt" => $Input['MediaExt'], "MediaCaption" => $Input['MediaCaption']));
        $this->db->insert('tbl_media', $InsertData);
        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } return array("EntityID" => $EntityID, "EntityGUID" => $EntityGUID);
    }

    public function sendFile($bucketName, $filename, $Name, $Folder){
        $names = $_FILES["File"]["name"];
        $ext = end((explode(".", $names)));
        $result = $this->S3->putObject(array(
                'Bucket' => SITE_NAME,
                'Key' => $Folder."/".$Name.".".$ext,
                'SourceFile' => $_FILES['File']['tmp_name'],
                'ContentType' => $_FILES['File']['type']
        ));
        return $result['ObjectURL'];
    }

    function uploadFile($UserID, $SectionID, $Path, $Ext = 'gif|jpg|png', $PostData = array()) {
        $FileName = get_guid();
        /** s3 file upload **/
        // if(S3_UPLOAD){
        //     $S3Url = $this->sendFile(SITE_NAME,$_FILES,$FileName,$PostData['Section']);
        // }
        checkDirExist($Path);
        $config['upload_path'] = $Path;
        $config['allowed_types'] = $Ext;
        $config['max_size'] = '250000';
        $config['max_width'] = '7000';
        $config['max_height'] = '7000';
        $config['file_ext_tolower'] = TRUE;
        $config['quality'] = '90%';
        $config['file_name'] = $FileName;
        $config['file_ext_tolower'] = TRUE;
        $this->load->library('upload', $config);
        if (!$this->upload->do_upload('File')) {
            echo $this->upload->display_errors();
            return FALSE;
        } else {
            $MediaDetails = $this->upload->data();
            if ($MediaDetails['file_type'] == 'video/mp4' || $MediaDetails['file_type'] == 'video/quicktime') {
                ob_start();
                system("ffmpeg -ss 4 -i $Path$FileName.mp4 -s 320x240 -frames:v 1 $Path$FileName.jpg 2>&1;");
                ob_clean();
            } 
            $MediaDetails = array("EntityGUID" => $FileName, "IsImage" => $MediaDetails['is_image'], "MediaRealName" => $MediaDetails['client_name'], "MediaName" => $MediaDetails['orig_name'], "MediaSize" => $MediaDetails['file_size'], "MediaExt" => $MediaDetails['file_ext'], "MediaCaption" => @$PostData['MediaCaption']);
            $MediaData = $this->addMedia($UserID, $SectionID, $MediaDetails);
            $Return = array("MediaID" => $MediaData['EntityID'], "MediaGUID" => $MediaData['EntityGUID'], "MediaURL" => realpath($Path . '/' . $MediaDetails['MediaName']), "MediaName" => $MediaDetails["MediaName"], "MediaExt" => $MediaDetails["MediaExt"]);
            return $Return;
        }
    }

    function resizePicture($SourcePath, $NewPath, $FileType, $FileName, $Sizes = array(), $Ratio = FALSE) {
        checkDirExist($NewPath);
        foreach ($Sizes as $key => $Size) {
            if (!$this->createThumb($SourcePath, ($key > 0 ? $NewPath . $Size . '_' . $FileName : $NewPath . $FileName), $FileType, $Size, $Size, ($Ratio ? '' : $Size))) {
                return FALSE;
            }
        } return TRUE;
    }

    function createThumb($SourcePath, $NewPath, $FileType, $Width, $Height, $MaintainRatio = '') {
        $SourceImage = FALSE;
        if (preg_match("/jpg|JPG|jpeg|JPEG/", $FileType)) {
            $SourceImage = imagecreatefromjpeg($SourcePath);
        } elseif (preg_match("/png|PNG/", $FileType)) {
            if (!$SourceImage = @imagecreatefrompng($SourcePath)) {
                $SourceImage = imagecreatefromjpeg($SourcePath);
            }
        } elseif (preg_match("/gif|GIF/", $FileType)) {
            $SourceImage = imagecreatefromgif($SourcePath);
        } if ($SourceImage == FALSE) {
            $SourceImage = imagecreatefromjpeg($SourcePath);
        } $OrigW = imageSX($SourceImage);
        $OrigH = imageSY($SourceImage);
        if ($OrigW < $Width && $OrigH < $Height) {
            $DesiredW = $OrigW;
            $DesiredH = $OrigH;
        } else {
            $Scale = min($Width / $OrigW, $Height / $OrigH);
            $DesiredW = ceil($Scale * $OrigW);
            $DesiredH = ceil($Scale * $OrigH);
        } if ($MaintainRatio != '') {
            $DesiredW = $DesiredH = $MaintainRatio;
        } $VirtualImage = imagecreatetruecolor($DesiredW, $DesiredH);
        if (preg_match("/png|PNG/", $FileType)) {
            imagealphablending($VirtualImage, false);
            imagesavealpha($VirtualImage, true);
        } else {
            $Kek = imagecolorallocate($VirtualImage, 255, 255, 255);
            imagefill($VirtualImage, 0, 0, $Kek);
        } if ($MaintainRatio == '') {
            imagecopyresampled($VirtualImage, $SourceImage, 0, 0, 0, 0, $DesiredW, $DesiredH, $OrigW, $OrigH);
        } else {
            $wm = $OrigW / $MaintainRatio;
            $Hm = $OrigH / $MaintainRatio;
            $Hheight = $MaintainRatio / 2;
            $Wheight = $MaintainRatio / 2;
            if ($OrigW > $OrigH) {
                $AdjustedWidth = $OrigW / $Hm;
                $HalfWidth = $AdjustedWidth / 2;
                $IntWidth = $HalfWidth - $Wheight;
                imagecopyresampled($VirtualImage, $SourceImage, -$IntWidth, 0, 0, 0, $AdjustedWidth, $MaintainRatio, $OrigW, $OrigH);
            } elseif (($OrigW <= $OrigH)) {
                $AdjustedHeight = $OrigH / $wm;
                $half_height = $AdjustedHeight / 2;
                imagecopyresampled($VirtualImage, $SourceImage, 0, 0, 0, 0, $MaintainRatio, $AdjustedHeight, $OrigW, $OrigH);
            } else {
                imagecopyresampled($VirtualImage, $SourceImage, 0, 0, 0, 0, $MaintainRatio, $MaintainRatio, $OrigW, $OrigH);
            }
        } if (preg_match("/png|PNG/", $FileType)) {
            $ImgC = imagepng($VirtualImage, $NewPath, 9);
        } else {
            $ImgC = imagejpeg($VirtualImage, $NewPath, 100);
        } if (@$ImgC) {
            imagedestroy($VirtualImage);
            imagedestroy($SourceImage);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function addMediaToEntity($MediaID, $UserID, $EntityID) {
        $this->db->limit(1);
        $this->db->where(array("MediaID" => $MediaID, "UserID" => $UserID, "EntityID" => null));
        $this->db->update('tbl_media', array("EntityID" => $EntityID));
        return TRUE;
    }

}
