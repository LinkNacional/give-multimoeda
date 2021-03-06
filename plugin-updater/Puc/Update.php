<?php
if ( !class_exists('Lkn_Puc_Update', false) ):

	/**
	 * A simple container class for holding information about an available update.
	 *
	 * @author Janis Elsts
	 * @access public
	 */
	abstract class Lkn_Puc_Update extends Lkn_Puc_Metadata {
	    public $slug;
	    public $version;
	    public $download_url;
	    public $translations = [];

	    /**
	     * @return string[]
	     */
	    protected function getFieldNames() {
	        return ['slug', 'version', 'download_url', 'translations'];
	    }

	    /**
	     * Format the JSON to wordpress format
	     */
	    public function toWpFormat() {
	        $update = new stdClass();

	        $update->slug = $this->slug;
	        $update->new_version = $this->version;
	        $update->package = $this->download_url;

	        return $update;
	    }
	}

endif;
