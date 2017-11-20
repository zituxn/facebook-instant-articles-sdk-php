<?hh // strict
/**
 * Copyright (c) 2016-present, Facebook, Inc.
 * All rights reserved.
 *
 * This source code is licensed under the license found in the
 * LICENSE file in the root directory of this source tree.
 */
namespace Facebook\InstantArticles\Client;

use Facebook\InstantArticles\Validators\Type;

class InstantArticleStatus
{
    const SUCCESS = 'success';
    const NOT_FOUND = 'not_found';
    const IN_PROGRESS = 'in_progress';
    const FAILED = 'failed';
    const UNKNOWN = 'unknown';

    /**
     * @var Vector<ServerMessage>
     */
    private Vector<ServerMessage> $messages = Vector {};

    /**
     * @var string Status message
     */
    private string $status = "";

    /**
     * Instantiates a new InstantArticleStatus object.
     *
     * @param string $status
     * @param ServerMessage[] $messages
     */
    public function __construct(string $status, Vector <ServerMessage> $messages)
    {
        Type::enforceWithin(
            $status,
            Vector {
                self::SUCCESS,
                self::NOT_FOUND,
                self::IN_PROGRESS,
                self::FAILED,
                self::UNKNOWN,
            }
        );
        $this->status = $status;
        $this->messages = $messages;
    }

    /**
     * Creates a instance from a status string,.
     *
     * @param string $status the status string, case insensitive.
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function fromStatus(string $status, Vector<ServerMessage> $messages): InstantArticleStatus
    {
        $status = strtolower($status);
        $validStatus = Type::isWithin(
            $status,
            Vector {
                self::SUCCESS,
                self::NOT_FOUND,
                self::IN_PROGRESS,
                self::FAILED,
            }
        );
        if ($validStatus) {
            return new self($status, $messages);
        } else {
            return new self(self::UNKNOWN, $messages);
        }
    }

    /**
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function success(Vector <ServerMessage> $messages = Vector {}): InstantArticleStatus
    {
        return new self(self::SUCCESS, $messages);
    }

    /**
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function notFound(Vector <ServerMessage> $messages = Vector {}): InstantArticleStatus
    {
        return new self(self::NOT_FOUND, $messages);
    }

    /**
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function inProgress(Vector <ServerMessage> $messages = Vector {}): InstantArticleStatus
    {
        return new self(self::IN_PROGRESS, $messages);
    }

    /**
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function failed(Vector <ServerMessage> $messages = Vector {}): InstantArticleStatus
    {
        return new self(self::FAILED, $messages);
    }

    /**
     * @param Vector<ServerMessage> $messages the message from the server
     *
     * @return InstantArticleStatus
     */
    public static function unknown(Vector <ServerMessage> $messages = Vector {}): InstantArticleStatus
    {
        return new self(self::UNKNOWN, $messages);
    }

    /**
     * @param ServerMessage $message
     */
    public function addMessage(ServerMessage $message): void
    {
        $this->messages->add($message);
    }

    /**
     * @return ServerMessage[]
     */
    public function getMessages(): Vector<ServerMessage>
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
