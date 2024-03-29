<?php  
/* 
Version: 1.0
Author: Felix Sułkowski
Website: http://behrooz.com
*/

class ControllerExtensionModuleBlogPopular extends Controller {
	public function index($setting) {
		
		$this->load->language('blog/blog');

		$this->load->model('blog/article');
                                
        $data['position'] = $setting['position'];
		
        if(isset($setting['heading_title'][$this->config->get('config_language_id')])){
            $data['heading_title'] = $setting['heading_title'][$this->config->get('config_language_id')];
        }else{
            $data['heading_title'] = $this->language->get('heading_popular_title');
        }
        
        $data['button_read_more'] = $this->language->get('button_read_more');
	
        $data['articles'] = array();

        $results = $this->model_blog_article->getPopularArticles($setting['articles_limit']);

        foreach ($results as $result) {
            
            $thumb = false;
            if(!empty($result['image'])){
                $thumb = $result['image'];
            }
            if($thumb){
                $this->load->model('tool/image');

                $thumb = $this->model_tool_image->resize($thumb, $setting['thumb_width'], $setting['thumb_height']);
            }
            
            $tags_array = array();
            
            if ($result['tags']) {
                 $tags = explode(',', $result['tags']);
                 foreach ($tags as $tag) {
                      $tags_array[] = array(
                           'tag'  => trim($tag),
                           'href' => $this->url->link('blog/blog', 'tag=' . trim(urlencode($tag)))
                      );
                 }
            }
            
            $data['articles'][] = array(
                'article_id'  => $result['article_id'],
                'title'        => $result['title'],
                'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
                'date_published'   =>  $result['date_published'],
                'thumb'     => $thumb,
                'tags' => $tags_array,
                'href'        => $this->url->link('blog/article', (isset($this->request->get['path']) ? 'path=' . $this->request->get['path'] . '&' : '') .'article_id=' . $result['article_id'])
            );
        }
	
		return $this->load->view('extension/module/blog_popular/'.str_replace('.tpl', '', $setting['template']), $data);
	}
}
?>