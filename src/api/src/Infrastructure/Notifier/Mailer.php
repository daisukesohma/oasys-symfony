<?php

declare(strict_types=1);

namespace App\Infrastructure\Notifier;

use App\Domain\Enum\TemplateEmailEnum;
use App\Infrastructure\Config\EnvVarHelper;
use DateTimeImmutable as UnsafeDateTimeImmutable;
use Safe\DateTimeImmutable;
use Swift_Attachment;
use Swift_Image;
use Swift_Mailer;
use Swift_Message;
use Twig\Environment;

final class Mailer
{
    private Swift_Mailer $mailer;
    private Environment $twig;
    private EnvVarHelper $envVarHelper;
    private string $mailerFrom;

    public function __construct(Swift_Mailer $mailer, Environment $twig, EnvVarHelper $envVarHelper)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->envVarHelper = $envVarHelper;
        $this->mailerFrom = $envVarHelper->fetch(EnvVarHelper::MAILER_FROM);
    }

    /**
     * @param mixed[] $context
     * @param Swift_Attachment[] $attachments
     * @param string[] $cc
     */
    public function send(string $to, string $subject, string $template, array $context = [], array $attachments = [], array $cc = []): int
    {
        $message = (new Swift_Message($subject))
            ->setFrom($this->mailerFrom, 'App Oasys')
            ->setTo($to)
            ->setCc($cc);

        foreach ($attachments as $attachment) {
            $message->attach($attachment);
        }

        $imgPath = $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . '/public/img/';

        $context['logo'] = $message->embed(Swift_Image::fromPath($imgPath . 'logo.png'));
        $context['background_image_purple'] = $message->embed(Swift_Image::fromPath($imgPath . 'bg-desktop-default-header.png'));
        $context['background_image_gray'] = $message->embed(Swift_Image::fromPath($imgPath . 'fond-gris.png'));

        $context['linkedin'] = $message->embed(Swift_Image::fromPath($imgPath . 'icon-linkedin.png'));
        $context['twitter'] = $message->embed(Swift_Image::fromPath($imgPath . 'twitter.png'));
        $context['website'] = $message->embed(Swift_Image::fromPath($imgPath . 'icon-site.png'));

        $context['site_oasis_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::SITE_OASYS_LINK);
        $context['linkedin_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::LINKEDIN_LINK);
        $context['twitter_link'] = $this->envVarHelper->fetch(TemplateEmailEnum::TWITTER_LINK);

        $message->setBody(
            $this->twig->render(
                $template,
                $context,
            ),
            'text/html'
        );

        return $this->mailer->send($message);
    }

    public function createAttachment(string $filename, string $content): Swift_Attachment
    {
        return new Swift_Attachment($content, $filename);
    }

    public function convertTimeToLocal(?UnsafeDateTimeImmutable $time): DateTimeImmutable
    {
        return new DateTimeImmutable($time !== null ? $time->format('c') : '');
    }
}
