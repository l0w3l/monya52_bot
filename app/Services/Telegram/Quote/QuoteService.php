<?php

declare(strict_types=1);

namespace App\Services\Telegram\Quote;

use App\Models\Quote;
use App\Services\QuoteApi\QuoteApiServiceInterface;
use App\Services\Telegram\File\FileServiceInterface;
use App\Services\Telegram\Stat\StatServiceInterface;
use App\Telegram\Keyboards\Inline\Stat\StatInlineKeyboardFactory;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\Type\InputFile;
use Phptg\BotApi\Type\Message;

class QuoteService extends AbstractService implements QuoteServiceInterface
{
    public function __construct(
        public StatServiceInterface $statService,
        public FileServiceInterface $fileService,
        public QuoteApiServiceInterface $quoteApiService,
    ) {}

    public function createFor(Message $message): Quote
    {
        $imagePath = $this->quoteApiService->getImageQuote($message);

        $quote = Quote::create([
            'text' => $message->text ?? '',
            'message_id' => $message->messageId,
            'chat_id' => $message->chat->id,
        ]);

        $stat = $this->statService->createFor($quote);

        $message = SpiritBox::sendSticker(InputFile::fromLocalFile($imagePath), replyMarkup: (new StatInlineKeyboardFactory)->make()->build(['stat' => $stat]));

        $this->fileService->createFor($message->sticker, $quote);

        return $quote;
    }

    public function findFor(Message $message): Quote
    {
        return Quote::where('message_id', $message->messageId)
            ->where('chat_id', $message->chat->id)
            ->where('text', $message->text)
            ->firstOrFail();
    }

    public function exists(Message $message): bool
    {
        return Quote::where('message_id', $message->messageId)
            ->where('chat_id', $message->chat->id)
            ->exists();
    }
}
