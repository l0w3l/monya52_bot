<?php

declare(strict_types=1);

namespace App\Services\QuoteApi;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Lowel\LaravelServiceMaker\Services\AbstractService;
use Lowel\Telepath\Facades\Extrasense;
use Lowel\Telepath\Facades\Paranormal;
use Lowel\Telepath\Facades\SpiritBox;
use Phptg\BotApi\FailResult;
use Phptg\BotApi\TelegramBotApi;
use Phptg\BotApi\Type\Chat;
use Phptg\BotApi\Type\File;
use Phptg\BotApi\Type\Message;
use Phptg\BotApi\Type\MessageOrigin;
use Phptg\BotApi\Type\MessageOriginChannel;
use Phptg\BotApi\Type\MessageOriginChat;
use Phptg\BotApi\Type\MessageOriginHiddenUser;
use Phptg\BotApi\Type\MessageOriginUser;
use Phptg\BotApi\Type\PhotoSize;
use Phptg\BotApi\Type\Sticker\Sticker;
use Phptg\BotApi\Type\User;

class QuoteApiService extends AbstractService implements QuoteApiServiceInterface
{
    public function getImageQuote(Message $message): string
    {
        $createForPathRelative = 'quotes/' . $message->messageId . '.webp';
        $from = $this->resolveFrom(
            $message->forwardOrigin ?? $message->from ?? $message->senderChat ?? null
        );

        /**
         * @var Response
         */
        $image = Http::baseUrl(config('quote.url'))
            ->asJson()
            ->post('/generate.webp', [
                'type' => 'quote',
                'format' => 'webp',
                'backgroundColor' => '#1b1429',
                'width' => 1024,
                'height' => 768,
                'scale' => 2,
                'messages' => [
                    [
                        'entities' => $message->entities,
                        'from' => $from,
                        'text' => $message->text ?? $message->caption,
                        'avatar' => true,
                        'media' => [
                            'url' => (empty($message->photo)) ? null : $this->resolveFileToUrl($message->photo[0]),
                        ],
                    ],
                ],
            ]);

        $result = Storage::disk('public')
            ->put($createForPathRelative, $image->body());

        if (! $result) {
            throw new \Exception('Failed to createFor quote image');
        }

        return Storage::disk('public')->path($createForPathRelative);
    }

    private function resolveFrom(User|Chat|MessageOrigin|null $user): ?array
    {
        if ($user instanceof MessageOriginHiddenUser) {
            return [
                'id' => 12345678,
                'name' => $user->senderUserName,
                'type' => 'user',
            ];
        } elseif ($user instanceof MessageOriginChat) {
            return [
                'id' => 12345678,
                'name' => $user->authorSignature,
                'type' => 'chat',
            ];
        } elseif ($user instanceof MessageOriginChannel) {
            return [
                'id' => 12345678,
                'name' => $user->authorSignature,
                'type' => 'channel',
            ];
        } elseif ($user instanceof MessageOriginUser) {
            return $this->resolveFrom($user->senderUser);
        } elseif ($user instanceof Chat || $user instanceof User) {
            return [
                'id' => $user->id,
                'first_name' => $user->firstName ?? '',
                'last_name' => $user->lastName,
                'username' => $user->username,
                'type' => ($user instanceof User) ? 'user' : 'chat',
                'photo' => $this->resolveAvatar($user),
            ];
        } else {
            return null;
        }
    }

    private function resolveAvatar(User|Chat $from): array
    {
        $photos = SpiritBox::getUserProfilePhotos($from->id);

        if ($photos instanceof FailResult || empty($photos->photos)) {
            if (config('telepath.profiles.clean.token') === null) {
                return [];
            }

            $cleanClient = new TelegramBotApi(config('telepath.profiles.clean.token'));
            $photos = $cleanClient->getUserProfilePhotos($from->id);

            if ($photos instanceof FailResult || empty($photos->photos)) {
                return [];
            } else {
                return [
                    'url' => $cleanClient->makeFileUrl($cleanClient->getFile($photos->photos[0][1]->fileId)),
                ];
            }
        } else {
            return [
                'url' => $this->resolveFileToUrl($photos->photos[0][1]),
            ];
        }
    }

    private function resolveFileToUrl(PhotoSize|Sticker $photo): string
    {
        return SpiritBox::makeFileUrl(SpiritBox::getFile($photo->fileId));
    }
}
