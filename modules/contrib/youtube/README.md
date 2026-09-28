# YouTube Field

The YouTube field module provides a simple field that allows you to add a
YouTube video to a content type, user, or any entity.

Display types include:

- YouTube videos of various sizes and options.
- YouTube thumbnails with image styles.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/youtube).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/youtube).

## Table of contents
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
- [Configuration](#configuration)
- [Maintainers](#maintainers)

## Requirements

This module requires no modules outside of Drupal core.

## Installation

Install as you would normally install a contributed Drupal module. For further
information, see
[Installing Drupal Modules](https://www.drupal.org/docs/extending-drupal/installing-drupal-modules).

## Usage

To use this module create a new field of type 'YouTube video'. This field will
accept YouTube URLs of the following formats:

- <http://youtube.com/watch?v=[video_id]>
- <http://youtu.be/[video_id]>
- <http://youtube.com/v/[video_id]>
- <http://youtube.com/embed/[video_id]>
- <http://youtube.com/?v=[video_id]>

All formats listed above can also be provided without 'http://', with 'www.',
or with 'https://' rather than 'http://'. The last format can be provided with
additional parameters (ignored) and v does not have to be the first parameter.

## Configuration

Global module settings can be found at admin/config/media/youtube.

The video output of a YouTube field can be manipulated in three ways:

- Global parameters found on the configuration page mentioned above.
- Field-specific parameters found in that particular field's display settings.
- Views settings for the specific field.

The thumbnail of the YouTube image can also be used and can link to either the
content, the video on YouTube, or nothing at all.

To configure the field settings:

1. Click 'manage display' on the listing of entity types.
2. Click the configuration gear to the right of the YouTube field.

## Maintainers

- Gus Childs - [guschilds](https://www.drupal.org/u/guschilds)
- Jen Lampton - [jenlampton](https://www.drupal.org/u/jenlampton)
- Yaroslav Lushnikov - [imyaro](https://www.drupal.org/u/imyaro)
