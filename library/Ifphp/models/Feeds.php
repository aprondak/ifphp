<?php
/**
 * This file will contain all the functionality to deal 
 * with the Feeds interaction with the Feeds Table or anything
 * to do with the Feed
 * 
 * @version 0.1
 * @author Akeem Philbert <akeemphilbert@gmail.com>
 * @package Ifphp
 * @subpackage models
 * @copyright 2009 Ifphp
 */

/**
 * @see Ifphp/core/AbstractModel
 */
require_once 'Ifphp/core/AbstractModel.php';
require_once 'Ifphp/dtos/Feed.php';
require_once 'Ifphp/dtos/Status.php';

/**
 * This class contains all the db interactions for the Feed
 */
class Feeds extends AbstractModel
{
    protected $_name = 'feeds';
    protected $_rowClass = 'Feed';
    protected $_dependentTables = array('Posts');
    protected $_referenceMap = array(
        'Categories'=>array(
            'columns'   => 'categoryId',
            'refTableClass' =>  'Categories',
            'refColumns'    =>  'id'
        )
    );


    /**
     * Get all available categories
     * 
     * @return Zend_Db_Table_Rowset
     */
    public function getAll($page=1,$limit=0)
    {
            $select = $this->getSelect();
            $select->where('statusId = ?',Status::ACTIVE);
            return $this->fetchAll($select,null,$page,$limit);
    }

    /**
     * Get Feed by Id
     *
     * @param $id
     * @return Feed
     */
    public function getById($id)
    {
            $select = $this->select();
            $select->where('id = ?',$id);
            $select->where('statusId = ?',Status::ACTIVE);
            return $this->fetchRow($select);
    }

    /**
     * Get feed by slug
     *
     * @param string $slug
     * @return Feed
     */
    public function getBySlug($slug)
    {
        $select = $this->select();
        $select->where('slug = ?',$slug);
        $select->where('statusId = ?',Status::ACTIVE);
        return $this->fetchRow($select);
    }

    /**
     * Get feed by site url
     *
     * @param string $url
     * @return Feed
     */
    public function getBySiteUrl($url)
    {
        $select = $this->select();
        $select->where('siteUrl = ?',$url);
        return $this->fetchRow($select);
    }
	
	
    /**
     * Returns the complete Select statement for a basic feed
     *
     * @return Zend_Db_Table_Select
     */
    public function getSelect(){
            $select = $this->select()->setIntegrityCheck(false)->from(array('feeds'=>'feeds'));
            $select->join(array('categories'=>'categories'), 'feeds.categoryId = categories.id',
                    array('category'=>'title'));
            $select->join(array('languages'=>'languages'),'feeds.languageId = languages.id',
                    array('language'=>'languages.title'));

            return $select;

    }

    /**
     * Returns the feeds that relate to a specific category
     *
     * @param int $categoryId
     * @return Zend_Db_Table_RowSet
     */
    public function getByCategory($categoryId)
    {

            $select = $this->getSelect();
            $select->where('categories.id = ?', $categoryId);
            $select->where('statusId = ?',Status::ACTIVE);
            return $this->fetchAll($select);

    }

    /**
     * Get recently updated feeds
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getRecentlyUpdated()
    {
        $select = $this->select();
        $select->order('lastPing desc');
        $select->where('statusId = ?',Status::ACTIVE);
        return $this->fetchAll($select);
    }

    /**
     * Get popular feeds
     *
     * @return Zend_Db_Table_Rowset
     */
    public function getPopular()
    {
        $select = $this->select();
        $select->order('views desc');
        $select->where('statusId = ?',Status::ACTIVE);
        return $this->fetchAll($select);
    }

    /**
     * Get feed by token
     *
     * @param string $token
     * @return Feed
     */
    public function getByToken($token)
    {
        $select = $this->select();
        $select->where('token = ?',$token);
        return $this->fetchRow($select);
    }

    /**
     * Get feed by email
     * 
     * @param string $email
     * @return Feed
     */
    public function getByEmail($email)
    {
        $select = $this->select()->setIntegrityCheck(false);
        $select->from('feeds');
        $select->join(array('users'), 'users.id = feeds.userId', array());
        $select->where('users.email = ?',$email);
        return $this->fetchAll($select);
    }

    public function getByUserId($userId)
    {
        $select = $this->select();
        $select->where('userId = ?',$userId);
        return $this->fetchAll($select);
    }

    /**
     * Get Feed using siteurl and userid
     * 
     * @param string $siteUrl
     * @param int $userId
     * @return Feed
     */
    public function getBySiteUrlAndUserId($siteUrl,$userId)
    {
        $select = $this->select();
        $select->where('siteUrl = ?',$siteUrl);
        $select->where('userId = ?',$userId);
        return $this->fetchRow($select);
    }
	
}