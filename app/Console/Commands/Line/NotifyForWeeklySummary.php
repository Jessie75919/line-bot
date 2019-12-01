<?php

namespace App\Console\Commands\Line;

use App\Models\WeightSetting;
use app\Services\LineBot\Liff\LiffService;
use Illuminate\Console\Command;
use LINE\LINEBot;
use LINE\LINEBot\Constant\Flex\BubleContainerSize;
use LINE\LINEBot\Constant\Flex\ComponentButtonStyle;
use LINE\LINEBot\Constant\Flex\ComponentFontSize;
use LINE\LINEBot\Constant\Flex\ComponentFontWeight;
use LINE\LINEBot\Constant\Flex\ComponentLayout;
use LINE\LINEBot\Constant\Flex\ComponentMargin;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\BoxComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\ButtonComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ComponentBuilder\TextComponentBuilder;
use LINE\LINEBot\MessageBuilder\Flex\ContainerBuilder\BubbleContainerBuilder;
use LINE\LINEBot\MessageBuilder\FlexMessageBuilder;
use LINE\LINEBot\TemplateActionBuilder\Uri\AltUriBuilder;
use LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder;

class NotifyForWeeklySummary extends Command
{
    protected $signature = 'line:notify-for-weekly-summary';
    protected $description = '每週體重記錄圖表總結';

    public function handle(LINEBot $lineBot)
    {
        \Log::channel('slack')->info("line:notify-for-weekly-summary starting...");

        $flex = $this->getMessagePanel();

        $weightSettings = WeightSetting::with('memory')
            ->where('enable_notification', 1)
            ->get();

        foreach ($weightSettings as $weightSetting) {
            $lineBot->pushMessage($weightSetting->memory->channel_id, $flex);
        }

        \Log::channel('slack')->info("line:notify-for-weekly-summary done !");
    }

    public function getMessagePanel()
    {
        return FlexMessageBuilder::builder()
            ->setAltText('😀 來回顧本週的記錄吧！')
            ->setContents(
                BubbleContainerBuilder::builder()
                    ->setBody($this->createBodyBlock())
                    ->setSize(BubleContainerSize::GIGA)
            );
    }

    private function createBodyBlock()
    {
        $title = TextComponentBuilder::builder()
            ->setText('🙂️ 一週過去囉！來回顧一下本週的記錄吧！')
            ->setColor('#3473c3')
            ->setWeight(ComponentFontWeight::BOLD)
            ->setSize(ComponentFontSize::LG);

        $desktopUrl = 'https://liff.line.me/'.LiffService::parsePageToken(config('line.link_of_weight_index'));

        $button = ButtonComponentBuilder::builder()
            ->setStyle(ComponentButtonStyle::PRIMARY)
            ->setColor("#179ea2")
            ->setMargin(ComponentMargin::XL)
            ->setAction(
                new UriTemplateActionBuilder(
                    '回顧本週的記錄',
                    config('line.link_of_weight_index').'?page=review',
                    new AltUriBuilder($desktopUrl.'?page=review')
                )
            );

        return BoxComponentBuilder::builder()
            ->setLayout(ComponentLayout::VERTICAL)
            ->setBackgroundColor('#fafafa')
            ->setPaddingAll('5%')
            ->setContents([$title, $button]);
    }
}
