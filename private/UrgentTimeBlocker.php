<?php
require '../config.php';

class UrgentTimeBlocker{

    private $connection;
    private $blockToPHArray = array();


    public function __construct()
    {
        global $con;
        $this->connection = $con;
    }


    public function checkMerge_ghList(){
    $userWithSameAmount = array();
    $get = $this->connection->query("SELECT * FROM merge_gh WHERE status = '' AND attachment = ''");
    $get->setFetchMode(PDO::FETCH_ASSOC);
    $get->execute();
    if ($get->rowCount() < 1){
        return false;
    }else{
        while($row = $get->fetch()){
            if (($this->getPHIDDetails($row['phID'],'amntPH') == $this->getGHIDDetails($row['ghID'],'amountGH')) && $this->showTimeExpiry($row['dateMerge_expires'])){
                /*array_push($userWithSameAmount,
                    $row['phID']." - ".$row['ghID']
                );*/
                if($this->updateExpiredPH($row['phID'],$this->getPHIDDetails($row['phID'],'participantID'),$row['mergeID'],$row['ghID'])){
                    echo "Success";
                }else{
                    echo "Failed";
                }
            }else{
                echo "No User is a Defaulter yet";
            }
        }
        //return $userWithSameAmount;
        //return true;
    }
}

public function getPHIDDetails($phID,$col){
    $get = $this->connection->prepare("SELECT * FROM providehelp WHERE phID = :phID");
    $get->bindValue(':phID',$phID);
    $get->setFetchMode(PDO::FETCH_ASSOC);
    $get->execute();
    if ($get->rowCount() < 1){
        return false;
    }else{
        while ($row = $get->fetch()){
            return $row[$col];
        }
        return true;
    }
}

public function getGHIDDetails($phID,$col){
    $get = $this->connection->prepare("SELECT * FROM gethelp WHERE ghID = :phID");
    $get->bindValue(':phID',$phID);
    $get->setFetchMode(PDO::FETCH_ASSOC);
    $get->execute();
    if ($get->rowCount() < 1){
        return false;
    }else{
        while ($row = $get->fetch()){
            return $row[$col];
        }
        return true;
    }
}

private function showTimeExpiry($date){
    $todaysDATE =  date('M d, Y H:i:00');
    $additional_time =  date('M d, Y H:i:00', strtotime("$date+6 hours"));
    if ($additional_time <= $todaysDATE){
        return true;
    }else{
        return false;
    }
}

private function updateExpiredPH($phID,$partID,$mergeID,$ghID){
    $update = $this->connection->prepare("UPDATE providehelp SET status = :stat WHERE phID = :phid AND participantID = :partID");
    $update->bindValue(':stat','Cancelled');
    $update->bindValue(':phid',$phID);
    $update->bindValue(':partID',$partID);
    $update->setFetchMode(PDO::FETCH_ASSOC);
    if ($update->execute()){
        $update2 = $this->connection->prepare("UPDATE participant SET status = :stat WHERE pid = :partID");
        $update2->bindValue(':stat','blocked');
        $update2->bindValue(':partID',$partID);
        $update2->setFetchMode(PDO::FETCH_ASSOC);
        if ($update2->execute()){
            $update3 = $this->connection->prepare("UPDATE merge_gh SET status = :stat, attachment = :att WHERE mergeID = :mergeID");
            $update3->bindValue(':stat','Cancelled');
            $update3->bindValue(':att','Cancelled');
            $update3->bindValue(':mergeID',$mergeID);
            $update3->setFetchMode(PDO::FETCH_ASSOC);
            if ($update3->execute()){
                $update4 = $this->connection->prepare("UPDATE gethelp SET merge = :merge WHERE ghID = :ghID");
                $update4->bindValue(':merge','NO');
                $update4->bindValue(':ghID',$ghID);
                $update4->setFetchMode(PDO::FETCH_ASSOC);
                if ($update4->execute()){
                    $update5 = $this->connection->prepare("UPDATE gethelp SET user_status = :stat WHERE participantID = :partID");
                    $update5->bindValue(':stat','blocked');
                    $update5->bindValue(':partID',$partID);
                    $update5->setFetchMode(PDO::FETCH_ASSOC);
                    if ($update5->execute()){
                        $update6 = $this->connection->prepare("UPDATE providehelp SET status = :stat WHERE merge = :merge AND participantID = :partID");
                        $update6->bindValue(':stat','Cancelled');
                        $update6->bindValue(':merge','NO');
                        $update6->bindValue(':partID',$partID);
                        $update6->setFetchMode(PDO::FETCH_ASSOC);
                        if ($update6->execute()){
                            $update7 = $this->connection->prepare("UPDATE referral SET status = :stat WHERE phID = :phID AND participantID = :partID");
                            $update7->bindValue(':stat','Cancelled');
                            $update7->bindValue(':phID', $phID);
                            $update7->bindValue(':partID',$partID);
                            $update7->setFetchMode(PDO::FETCH_ASSOC);
                            if ($update7->execute()){
                                return true;
                            }
                            else{
                                return false;
                            }
                        }else{
                            return false;
                        }
                    }else{
                        return false;
                    }
                }else{
                    return false;
                }}else{
                return false;
            }
        }else{
            return false;
        }
    }else{
        return false;
    }
}

private function returnMoney(){

}


// This checks for a user who pHed a certian amount

    public function BlockPHToGH ($phID = ""){
        $this->connection->beginTransaction();
        $getallPH = $this->connection->prepare("select * from providehelp where merge = 'complete' and balance = :bal and status = :status");
        $getallPH->bindValue(':bal', 0);
        $getallPH->bindValue(':status', 'Unconfirmed');
        $getallPH->setFetchMode(PDO::FETCH_ASSOC);
        $getallPH->execute();
        if($getallPH->rowCount() < 1)
        {
            echo false;
        }
        else
        {
            while($row = $getallPH->fetch())
            {
                if($this->solveMergeId($this->CheckExpiredDateFromMerge($row['phID']))){
                    echo "sucess";
                }else{
                    echo $this->solveMergeId($this->CheckExpiredDateFromMerge($row['phID']));
                     //$this->solveMergeId($this->CheckExpiredDateFromMerge($row['phID']));
                }
                //array_push($this->blockToPHArray, $row['participantID']);
                //echo $row['participantID']."\r\n";
            }
            $this->connection->commit();

           // return $this->blockToPHArray;
        }
    }

    public function CheckExpiredDateFromMerge($phID){
        $searchMgt = $this->connection->prepare("SELECT * FROM `merge_gh` where phID=:phID and attachment='' and status=''");
        $searchMgt->bindValue(':phID', $phID);
        $searchMgt->setFetchMode(PDO::FETCH_ASSOC);
        $searchMgt->execute();
        if ($searchMgt->rowCount() < 1){
            return false;
        }else{
            while($row = $searchMgt->fetch()){
                if ($this->showTimeExpiry($row['dateMerge_expires'])){
                    return $row['mergeID'];
                }else{
                    return false;
                }
                //print_r($row);
            }
        }
    }

    public function solveMergeId($mergeID)
    {
        try {
            //$this->connection->beginTransaction();
            $getMerge = $this->connection->prepare("SELECT * FROM `merge_gh` where mergeID = :mergeID and attachment = '' and status=''");
            $getMerge->bindValue(':mergeID', $mergeID);
            $getMerge->setFetchMode(PDO::FETCH_ASSOC);
            $getMerge->execute();
            if ($getMerge->rowCount() < 1) {
                echo "No Expired User";
            } else {
                while ($row = $getMerge->fetch()) {
                    $ghID = $row['ghID'];
                    $phID = $row['phID'];
                    $amountGH = $row['amountGH'];
                    $partID = $row['participantID'];
                    $gHpartID = $row['gh_participantID'];
                    $update1 = $this->connection->prepare("update providehelp set status = :status where phID = :phID and amntPH = :amntPH");
                    $update1->bindValue(':status', 'Cancelled');
                    $update1->bindValue(':phID', $phID);
                    $update1->bindValue(':amntPH', $amountGH);
                    $update1->setFetchMode(PDO::FETCH_ASSOC);
                    if ($update1->execute()) {
                        $update1->closeCursor();
                        $update2 = $this->connection->prepare("UPDATE referral SET status = :stat WHERE phID = :phID AND participantID = :partID");
                        $update2->bindValue(':stat', 'Cancelled');
                        $update2->bindValue(':phID', $phID);
                        $update2->bindValue(':partID', $partID);
                        $update2->setFetchMode(PDO::FETCH_ASSOC);
                        if ($update2->execute()) {
                            $update2->closeCursor();
                            $update3 = $this->connection->prepare("UPDATE merge_gh SET status = :stat , attachment = :attach WHERE phID = :phID AND participantID = :partID AND ghID=:ghID");
                            $update3->bindValue(':stat', 'Cancelled');
                            $update3->bindValue(':attach', 'Cancelled');
                            $update3->bindValue(':phID', $phID);
                            $update3->bindValue(':ghID', $ghID);
                            $update3->bindValue(':partID', $partID);
                            $update3->setFetchMode(PDO::FETCH_ASSOC);
                            if ($update3->execute()) {
                                $update3->closeCursor();
                                $update4 = $this->connection->prepare("UPDATE gethelp SET balance = :balance, merge= 'partial' WHERE ghID = :ghID AND participantID = :gHpartID");
                                $update4->bindValue(':balance', ($this->getGHIDDetails($ghID, 'balance') + $amountGH));
                                $update4->bindValue(':ghID', $ghID);
                                $update4->bindValue(':gHpartID', $gHpartID);
                                $update4->setFetchMode(PDO::FETCH_ASSOC);

                                if ($update4->execute()) {
                                    $update4->closeCursor();
                                    $update5 = $this->connection->prepare("UPDATE gethelp SET user_status = :status WHERE participantID = :partID");
                                    $update5->bindValue(':status', 'blocked');
                                    $update5->bindValue(':partID', $partID);
                                    $update5->setFetchMode(PDO::FETCH_ASSOC);

                                    if ($update5->execute()) {
                                        $update5->closeCursor();
                                        $update6 = $this->connection->prepare("UPDATE participant SET status = :stat WHERE pid= :partID");
                                        $update6->bindValue(':stat', 'blocked');
                                        $update6->bindValue(':partID', $partID);
                                        $update6->setFetchMode(PDO::FETCH_ASSOC);
                                        if ($update6->execute()) {
                                            $update6->closeCursor();
                                            return true;
                                        } else {
                                            throw new PDOException("Error From Execute 6");
                                        }
                                    } else {
                                        throw new PDOException("Error From Execute 5");
                                    }
                                } else {
                                    throw new PDOException("Error From Execute 4");
                                }
                            } else {
                                throw new PDOException("Error From Execute 3");
                            }
                        } else {
                            throw new PDOException("Error From Execute 2");
                        }
                    } else {
                        throw new PDOException("Error From Execute 1");
                    }
                }
            }
        }catch (PDOException $e){
            return $e->getMessage();
        }
    }
}

    $obj = new UrgentTimeBlocker();
    //$obj->checkMerge_ghList();
    $obj->BlockPHToGH();

?>