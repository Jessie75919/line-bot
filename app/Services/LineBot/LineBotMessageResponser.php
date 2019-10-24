<?php

namespace App\Services\LineBot;

use App\Models\Memory;
use App\Models\Message;

class LineBotMessageResponser
{
    const GENERAL_RESPONSE = '好喔～好喔～';
    const ERROR_MESSAGE = 'Oh!oh!好像那裡有問題了 QQ';
    private $channelId;
    private $purpose;
    private $content;

    public function __construct($channelId, $purpose, $content = null)
    {
        $this->channelId = $channelId;
        $this->purpose = $purpose;
        $this->content = $content;
    }

    /**
     * @param $userMsg
     * @return string
     */
    public function keywordReply($userMsg): ?string
    {
        $resp = Message::where('keyword', $userMsg)
            ->where('channel_id', $this->channelId)->get();

        return count($resp) != 0 ? $resp->random()->message : null;
    }

    /**
     * @param  bool  $shutUp
     */
    public function setTalk(bool $shutUp)
    {
        Memory::where('channel_id', $this->channelId)->update(['is_talk' => $shutUp]);
    }

    /**
     * @return string
     */
    public function responseToUser(): ?string
    {
        switch ($this->purpose) {
            case 'response':
                return $this->content;
            case 'help':
                return $this->getHelpDescription();
            case 'talk':
                return $this->keywordReply($this->content);
            case 'speak':
                $this->setTalk(1);
                return self::GENERAL_RESPONSE;
            case 'shutUp':
                $this->setTalk(0);
                return self::GENERAL_RESPONSE;
            case 'state':
                $isTalk = Memory::where('channel_id', $this->channelId)->first()->is_talk;
                $stateText = "channel_id : \n [ {$this->channelId } \n";

                return ! $isTalk ? $stateText." 目前處於 \n [ 閉嘴狀態 ]" : $stateText." 目前處於 \n [可以講話狀態 ]";
        }
    }

    /**
     * @param  mixed  $purpose
     */
    public function setPurpose($purpose)
    {
        $this->purpose = $purpose;
    }

    /**
     * @param  null  $content
     * @return LineBotMessageResponser
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getHelpDescription()
    {
        $delimiter = str_replace('|', ' 或 ', TypePayloadHandler\TextTypePayloadHandler::DELIMITER_USE);
        return "#help 幫助文件 \n ".
            "## 分段符 ： {$delimiter} \n ".
            "    ex : 提醒;今天早上九點;吃早餐 \n ".
            "    ex : 提醒 x 今天早上九點 x 吃早餐 \n ".
            "    ex : 提醒，今天早上九點，吃早餐 \n ".
            "## 指令 : \n ".
            "  ### 提醒指令： 再指定的時間跳出訊息提示 \n".
            "      關鍵字 => [提醒、reminder、rem]\n ".
            "      新增提醒 \n ".
            "        - 提醒;今天早上九點;吃早餐 \n ".
            "        - reminder;今天早上9:00;吃早餐 \n ".
            "        - rem;後天早上九點半;吃早餐 \n ".
            "        - rem;2019-07-02 09:00;吃早餐 \n ".
            "      查詢所有提醒 \n ".
            "        - rem;all \n ".
            "      刪除某一筆提醒 \n ".
            "        - rem;del;{提醒Id} \n ".
            "  ### 學習關鍵字回應指令： 輸入 apple 機器人回應你蘋果 \n".
            "      關鍵字 => [學、learn]\n ".
            "        - 學;apple;蘋果 \n ".
            "        - learn;apple;蘋果 \n ".
            "  ### 狀態查詢指令： 回應機器人目前的狀態 \n".
            "      關鍵字 => [ jc ]\n ".
            "        - jc 狀態 \n ";
    }
}
