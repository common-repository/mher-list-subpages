<?php
namespace mher\listSubpages;
class ListSubpages
{
    private CONST NOBLOCKS = '-';
    private Options $options;

    public function __construct()
    {
        add_shortcode('mher_subpages', [$this, 'list_subpages']);
        $this->options = Options::getInstance();
    }

    /**
     * @param array $atts
     * @return string
     */
    public function list_subpages(array $atts): string
    {
        $post = \get_post();

        extract(shortcode_atts([
            'blocks_named' => 'teaser', // '-' for no content from subpages
            'image_id' => null, // the image id to use as a fallback, if none is specified the configured fallback image will be used, if none configured the built in will be used
            'image_size' => 'thumbnail', // the name of a (registered) image_size to use for rendering the image (and img-url)
            'template' => null, // the name or number of a user defined template to use instead of the built in template
            'list' => null, // a list page_ids of pages INSTEAD of the subpages of the current page!
            'exclude' => null, // a list of page_ids of pages to not display (should be combinable with list - even though it doesn't make much sense)
            'append' => null, // a list of page_ids of pages to add to the list of pages to show (should be combinable with list - even though it doesn't make much sense)
	        'prepend' => null, // a list of page_ids of pages to prepend to the list of pages to show (should be combinable with list - even though it doesn't make much sense)
        ], $atts));

		$list = Helpers::normalize_csv_list($list);
		$exclude = Helpers::normalize_csv_list($exclude, true);
		$append = Helpers::normalize_csv_list($append);
	    $prepend = Helpers::normalize_csv_list($prepend);

		$image_size = Helpers::validate_image_size($image_size);

        $fallback_image_id = $this->get_fallback_image_id($image_id);

        $pages = $this->get_the_pages($post, $list, $exclude, $prepend, $append);
        $pages_html = [];
        foreach ($pages as $page) {
            $pages_html[] = $this->get_subpagerow_html($page, $blocks_named, $image_size, $fallback_image_id, $template);
        }

        $pages_rows = implode('', $pages_html);

        $the_template = $this->get_template($template);

        $html = $the_template['templatehead'] . $pages_rows . $the_template['templatefoot'];

        return Helpers::remove_html_comments(Helpers::remove_scripts(Helpers::remove_events($html)));
    }

    /**
     * @param \WP_Post $post
     * @param string|null $list
     * @param string|null $exclude
     * @param string|null $include
     * @return array
     */
    private function get_the_pages(\WP_Post|null $post, string|null $list, string|null $exclude, string|null $prepend,  string|null $append): array
    {
		if (is_null($post) and is_null($list)) {
			return [];
		}

        $exclude_ids = Helpers::csv_list_to_int_array($exclude);
        if (is_null($list)) {
            $pages = $this->get_subpages($post, $exclude);
        } else {
            $ids = Helpers::csv_list_to_int_array($list);
            $pages = $this->get_pages_by_ids($ids, $exclude_ids);
        }

		$prepend_ids = Helpers::csv_list_to_int_array($prepend);
		$prepend_pages = $this->get_pages_by_ids($prepend_ids, $exclude_ids);

        $append_ids = Helpers::csv_list_to_int_array($append);
        $append_pages = $this->get_pages_by_ids($append_ids, $exclude_ids);

        return array_merge($prepend_pages, $pages, $append_pages);
    }

    /**
     * @param \WP_Post $post
     * @param string $exclude
     * @return array
     */
    private function get_subpages(\WP_Post $post, string|null $exclude): array
    {
        $args = [
            'sort_order' => 'ASC',
            'sort_column' => 'menu_order',
            'parent' => $post->ID,
            'post_status' => 'publish',
        ];

		$exclude_ids = Helpers::csv_list_to_int_array($exclude);
		$allpages = \get_pages($args);

		$pages = [];
		foreach ($allpages as $page) {
			if (!in_array($page->ID, $exclude_ids ) ) {
				$pages[] = $page;
			}
		}

        return $pages;
    }

    private function get_pages_by_ids(array $ids, array $exclude_ids): array
    {
		$pages = [];
        foreach ($ids as $id) {
            if (!in_array($id, $exclude_ids)) {
                $pages[] = \get_post($id);
            }
        }
        return $pages;
    }

    /**
     * @param string|null $template_name
     * @return string[]
     */
    private function get_template(string|null $template_name = null): array
    {
	    $default_template = [
            'templatehead' => '<table class="default_template_head" style="border: 1px black solid; border-collapse: collapse; margin: 1.5em 0;"><tbody>',
            'templaterow' => '<tr><td style="width: 33.33%; padding: 1em; vertical-align: middle; text-align: center;"><a href="{{ url }}">{{ image }}</a></td><td style="width: 66.66%; padding: 1em; vertical-align: middle"><h4><a href="{{ url }}">{{ title }}</a></h4>{{ blocks }}</td></tr>',
            'templatefoot' => '</tbody></table>'
        ];

        $template = $this->options->get_template($template_name);

        return $template ?? $default_template;
    }

	/**
	 * @param \WP_Post $subpage
	 * @param string $blocks_named
	 * @param string $image_size
	 * @param int|null $image_id
	 * @param string|null $template_name
	 *
	 * @return string
	 */
    private function get_subpagerow_html(\WP_Post $subpage, string $blocks_named, string $image_size, int|null $image_id = null, string|null $template_name = null): string
    {
        if ($subpage === null) {
            return '';
        }

        $blocks = $this->get_rendered_blocks_by_name($subpage->post_content, $blocks_named);

        $title = \get_the_title($subpage);
        $url = \get_permalink($subpage);
        $image = $this->get_image($subpage, $image_size, $image_id);
        $image_url = $this->get_image_url($subpage, $image_size, $image_id);

        $template = $this->get_template($template_name)['templaterow'];

        return str_replace(['{{ url }}', '{{ image }}', '{{ title }}', '{{ blocks }}', '{{ image_url }}'], [$url, $image, $title, $blocks, $image_url], $template);
    }

    /**
     * @param string $content
     * @param string $blocks_named
     * @return string
     */
    private function get_rendered_blocks_by_name(string $content, string $blocks_named): string
    {
        if ($blocks_named === $this::NOBLOCKS) {
            return '';
        }

        $blocks = $this->find_blocks_by_name($content, $blocks_named);
        return $this->render_blocks($blocks);
    }

	/**
	 * @param \WP_Post $subpage
	 * @param string $image_size
	 * @param int|null $image_id
	 *
	 * @return string
	 */
    private function get_image(\WP_Post $subpage, string $image_size, int|null $image_id = null): string
    {
        if (!is_null($subpage) && \has_post_thumbnail($subpage)) {
            $image = \get_the_post_thumbnail($subpage, $image_size);
        } else {
            $image = '<img src="' . $this->get_image_url($subpage, $image_size, $image_id) . '" alt="' . \get_the_title($subpage) . '" />';
        }
        return $image;
    }

	/**
	 * @param \WP_Post $subpage
	 * @param string $image_size
	 * @param int|null $image_id
	 *
	 * @return string
	 */
    private function get_image_url(\WP_Post $subpage, string $image_size, int|null $image_id = null): string
    {
        if (!is_null($subpage) && \has_post_thumbnail($subpage)) {
            $image_url = \get_the_post_thumbnail_url($subpage, $image_size);
        } elseif (!is_null($image_id)) {
            $image_url = \wp_get_attachment_image_url($image_id, $image_size);
        } else {
            $image_url = \plugin_dir_url(__FILE__) . 'images/fallback-image.webp';
        }
        return $image_url;
    }

    /**
     * @param string $content
     * @param string $name
     * @return array
     */
    private function find_blocks_by_name(string $content, string $name): array
    {
        if (!\has_blocks($content)) {
            return [];
        }

        $blocks = \parse_blocks($content);

        $found_blocks = [];
        foreach ($blocks as $block) {
            if (isset($block['attrs']['metadata']['name']) && $block['attrs']['metadata']['name'] == $name) {
                $found_blocks[] = $block;
            }
        }

        return $found_blocks;
    }

    /**
     * @param array $blocks
     * @return string
     */
    private function render_blocks(array $blocks): string
    {
        $rendered_blocks = '';

        foreach ($blocks as $block) {
            $rendered_blocks .= \render_block($block);
        }

        return $rendered_blocks;
    }

	/**
	 * @param string|null $image_id
	 *
	 * @return int|null
	 */
	private function get_fallback_image_id(string|null $image_id): int|null {
        // if the image_id in the shortcode is set, it takes precedence over the generally configured fallback image
        if (is_null($image_id) || $image_id === '') {
            $fallback_image_id = $this->options->get_image_id();
        } else {
            $fallback_image_id = $image_id;
        }
        // if the fallback_image_id is not null, make sure it's an integer
        if (!is_null($fallback_image_id)) {
            $fallback_image_id = intval($fallback_image_id);
        }

        return $fallback_image_id === 0 ? null : $fallback_image_id;
    }
}