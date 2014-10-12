<?php

    namespace G\ImagesBundle\Resizer;

    use Imagine\Image\ImagineInterface;
    use Imagine\Image\Box;
    use Gaufrette\File;
    use Sonata\MediaBundle\Model\MediaInterface;
    use Sonata\MediaBundle\Resizer\ResizerInterface;
    use Imagine\Image\ImageInterface;
    use Imagine\Exception\InvalidArgumentException;
    use Sonata\MediaBundle\Metadata\MetadataBuilderInterface;

    class CustomResizer implements ResizerInterface
    {
        protected $adapter;
        protected $mode;
        protected $metadata;

        /**
         * @param ImagineInterface $adapter
         * @param string $mode
         */
        public function __construct(ImagineInterface $adapter, $mode, MetadataBuilderInterface $metadata)
        {
            $this->adapter = $adapter;
            $this->mode = $mode;
            $this->metadata = $metadata;
        }

        /**
         * {@inheritdoc}
         */
        public function resize(MediaInterface $media, File $in, File $out, $format, array $settings)
        {
            if (!(isset($settings['width']) && $settings['width']))
                throw new \RuntimeException(sprintf('Width parameter is missing in context "%s" for provider "%s"', $media->getContext(), $media->getProviderName()));

            $size = $this->getBox($media, $settings);
            $mode      = \Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND;

            $resizeimg = $this->adapter->load($in->getContent())
                       ->thumbnail($size, $mode);

            $sizeR     = $resizeimg->getSize();
            $widthR    = $sizeR->getWidth();
            $heightR   = $sizeR->getHeight();

            $content  = $this->adapter->create($size);
            $startX = $startY = 0;
            if ( $widthR < $settings['width'] ) {
                $startX = ( $settings['width'] - $widthR ) / 2;
            }
            if ( $heightR < $settings['height'] ) {
                $startY = ( $settings['height'] - $heightR ) / 2;
            }
            $content->paste($resizeimg, new \Imagine\Image\Point($startX, $startY))
                ->get($format, array('quality' => $settings['quality']));

            $out->setContent($content, $this->metadata->get($media, $out->getName()));
        }

        /**
         * {@inheritdoc}
         */
        public function getBox(MediaInterface $media, array $settings)
        {
            $size = $media->getBox();
            $hasWidth = isset($settings['width']) && $settings['width'];
            $hasHeight = isset($settings['height']) && $settings['height'];

            if (!$hasWidth && !$hasHeight)
                throw new \RuntimeException(sprintf('Width/Height parameter is missing in context "%s" for provider "%s". Please add at least one parameter.', $media->getContext(), $media->getProviderName()));

            if ($hasWidth && $hasHeight)
                return new Box($settings['width'], $settings['height']);

            if (!$hasHeight)
                $settings['height'] = intval($settings['width'] * $size->getHeight() / $size->getWidth());

            if (!$hasWidth)
                $settings['width'] = intval($settings['height'] * $size->getWidth() / $size->getHeight());

            return $this->computeBox($media, $settings);
        }

        /**
         * @throws InvalidArgumentException
         *
         * @param MediaInterface $media
         * @param array $settings
         *
         * @return Box
         */
        private function computeBox(MediaInterface $media, array $settings)
        {
            if ($this->mode !== ImageInterface::THUMBNAIL_INSET && $this->mode !== ImageInterface::THUMBNAIL_OUTBOUND)
                throw new InvalidArgumentException('Invalid mode specified');

            $size = $media->getBox();

            $ratios = [
                $settings['width'] / $size->getWidth(),
                $settings['height'] / $size->getHeight()
            ];

            if ($this->mode === ImageInterface::THUMBNAIL_INSET)
                $ratio = min($ratios);
            else
                $ratio = max($ratios);

            return $size->scale($ratio);
        }
    }