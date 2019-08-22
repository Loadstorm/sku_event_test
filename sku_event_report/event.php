<?php
class Event {

    //fields to store
    private $id;
    private $creDate;
    private $creTime;
    private $userId;
    private $ip;
    private $type;
    private $subject;
    private $body;
    private $errMessage;
    private $bodyData;
    private $link;

    //set and get fields
    function setId($id){
        $this->id = $id;
    }

    function getId(){
        return $this->id;
    }

    function setCreDate($creDate){
        $this->creDate = $creDate;
    }

    function getCreDate(){
        return $this->creDate;
    }

    function setCreTime($creTime){
        $this->creTime = $creTime;
    }

    function getCreTime(){
        return $this->creTime;
    }

    function setUserId($userId){
        $this->userId = $userId;
    }

    function getUserId(){
        return $this->userId;
    }

    function setIp($ip){
        $this->ip = $ip;
    }

    function getIp(){
        return $this->ip;
    }

    function setType($type){
        $this->type = $type;
    }

    function getType(){
        return $this->type;
    }

    function setSubject($subject){
        $this->subject = $subject;
    }

    function getSubject(){
        return $this->subject;
    }

    function setBody($body){
        $this->body = $body;
    }

    function getBody(){
        return $this->body;
    }

    function setErrMessage($errMessage){
        $this->errMessage = $errMessage;
    }

    function getErrMessage(){
        return $this->errMessage;
    }

    function setBodyData($bodyData){
        $this->bodyData = $bodyData;
    }

    function getBodyData(){
        return $this->bodyData;
    }

    function setLink($link){
        $this->link = $link;
    }

    function getLink(){
        return $this->link;
    }
}