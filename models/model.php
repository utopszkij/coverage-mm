<?php
	/**
	* coverage-mm base model 
	* every model is a descendant of this
	*/

	class Model {
	    protected $controller;
	    protected $modelName = 'model';
	    protected $lastError = '';
	    
		/**
		 * php object constructor
		 */ 
		function __construct($controller) {
		    $this->controller = $controller;
		}
	
		/**
		 * get last action errorMsg. if OK result = '';
		 * @return string
		 */
		public function getErrorMsg(): string {
		    return $this->lastError;
		}
		
		// ====================== ACF ==============================
		// plugin init-be kell:
		// apply_filters('acf/settings/l10n_texdomain','cmm');
		/**
		 * get ACF group id  -return 0 if not exists
		 * @param string $groupTitle
		 * @return int
		 */
		public function getAcfGroupId(string $groupTitle): int {
		    $result = 0;
		    $posts = get_posts( array(
		        'post_type' => 'acf-field-group',
		        'post_status' => 'publish',
		        'posts_per_page' => 100
		    ));
		    foreach ($posts as $post) {
		        if ($post->post_title == $groupTitle) {
		            $result = $post->ID;
		        }
		    }
		    return $result;
		}
		
		/**
		 * get ACF filed ID    -return 0 if not exists
		 * @param string $filedName
		 * @return int
		 */
		public function getAcfFieldId(string $fieldTitle, int $groupId): int {
		    $result = 0;
		    $posts = get_posts( array(
		        'post_type' => 'acf-field',
		        'post_status' => 'publish',
		        'posts_per_page' => 100
		    ));
		    foreach ($posts as $post) {
		        if (($post->post_title == $fieldTitle) & ($post->post_parent == $groupId)) {
		            $result = $post->ID;
		        }
		    }
		    return $result;
		}
		
		/**
		 * add new ACF group definition, return new grouo ID
		 * @param string $groupTitle  
		 * @param string $content
		 * @return int
		 */
		public function addAcfGroup(string $groupTitle, string $content):int {
		    $newPost = array(
		        'post_title'     => $groupTitle,
		        'post_excerpt'   => sanitize_title( $groupTitle ),
		        'post_name'      => 'group_' . uniqid(),
		        'post_date'      => date( 'Y-m-d H:i:s' ),
		        'comment_status' => 'closed',
		        'post_status'    => 'publish',
		        'post_content'   => $content,
		        'post_type'      => 'acf-field-group',
		    );
		    return wp_insert_post( $newPost );
		}
		
        /**
         * add new ACF field definition
         * @param int $groupId
         * @param string $fieldName
         * @param string $content
         */
		public function addAcfField(int $groupId, string $fieldName, string $content) { 
		    $newPost = array(
		            'post_title'     => $fieldName,
		            'post_excerpt'   => $fieldName,
		            'post_name'      => 'field_' . uniqid(),
		            'post_date'      => date( 'Y-m-d H:i:s' ),
		            'comment_status' => 'closed',
		            'post_status'    => 'publish',
		            'post_parent'    => $groupId,
		            'post_content'   => $content,
		            'post_type'      => 'acf-field',
		    );
	        wp_insert_post( $newPost );
		}
		
	} // Model class
?>