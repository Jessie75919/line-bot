<?php

namespace App\Console\Commands\Line;

use Illuminate\Console\Command;
use LINE\LINEBot;
use LINE\LINEBot\Constant\Flex\BubleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\Constant\Flex\ComponentSpacing;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;

class NotifyToClockInOut extends Command
{
    protected $signature = 'line:notify-to-clock-in-out';
    protected $description = '提醒上下班記得打卡';

    private static function createBodyBlock($wordings)
    {
        $title = TextComponentBuilder::builder()
            ->setText($wordings['title'])
            ->setColor($wordings['title_color'])
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::XL);

        $wordingSection = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::BASELINE)
            ->setSpacing(ComponentSpacing::XL)
            ->setContents([
                TextComponentBuilder::builder()
                    ->setText($wordings['wording'])
                    ->setColor($wordings['wording_color'])
                    ->setWeight(ComponentFontWeight::BOLD)
                    ->setSize(ComponentFontSize::LG)
                    ->setFlex(1),
            ]);

        $info = BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setMargin(ComponentMargin::XL)
            ->setSpacing(ComponentSpacing::XL)
            ->setContents([$wordingSection]);

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#fafafa')
            ->setPaddingAll('5%')
            ->setContents([$title, $info]);
    }

    public function handle(LINEBot $lineBot)
    {
        $isMorning = now('Asia/Taipei')->hour <= 12;
        $mode = $isMorning ? 'on' : 'off';
        $wording = [
            'on' => [
                'title' => 'Good Morning 早安~',
                'title_color' => '#ff7b4a',
                'wording' => '又要上班了 Q_Q 上班哭哭之餘也記得要打卡喔！',
                'wording_color' => '#667F99',
            ],
            'off' => [
                'title' => 'Good Night 晚安...',
                'title_color' => '#667F99',
                'wording' => '終於下班了！也別忘了要打卡喔～',
                'wording_color' => '#78AAAA',
            ],
        ];

        $flex = FlexMessageBuilder::builder()
            ->setAltText($wording[$mode]['wording'])
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setBody(
                        self::createBodyBlock($wording[$mode])
                    )
                    ->setSize(BubleContainerSize::GIGA)
            );

        /* company group */
        $lineBot->pushMessage('C728ab38fdce52d7a538de2dc738d4723', $flex);
        /* jc */
        //        $lineBot->pushMessage('U1f1c825c5fc74690c561e00c1a0c3f48', $flex);
    }
}
