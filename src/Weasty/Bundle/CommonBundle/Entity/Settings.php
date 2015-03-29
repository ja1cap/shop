<?php

namespace Weasty\Bundle\CommonBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class Settings
 * @package Weasty\Bundle\CommonBundle\Entity
 */
class Settings extends AbstractEntity
{

    const TEMPLATE_VERSION_DEFAULT = 1;
    const TEMPLATE_VERSION_LEBEDEV = 2;

    public static $version_names = [
        self::TEMPLATE_VERSION_DEFAULT => 'default',
        self::TEMPLATE_VERSION_LEBEDEV => 'lededev',
    ];

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string
     */
    private $keywords;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $logo;

    /**
     * @var \Application\Sonata\MediaBundle\Entity\Media
     */
    private $favicon;

    /**
     * @var string
     */
    private $admin_email;

    /**
     * @var string
     */
    private $manager_email;

    /**
     * @var string
     */
    private $mailer_host;

    /**
     * @var string
     */
    private $mailer_port;

    /**
     * @var string
     */
    private $mailer_user;

    /**
     * @var string
     */
    private $mailer_password;

    /**
     * @var string
     */
    protected $vk_url;

    /**
     * @var string
     */
    protected $fb_url;

    /**
     * @var string
     */
    protected $google_url;

    /**
     * @var string
     */
    protected $email;

    /**
     * @var string
     */
    protected $customer_email_template;

    /**
     * @var string
     */
    protected $admin_email_template;

    /**
     * @var string
     */
    protected $manager_email_template;

    /**
     * @var integer
     */
    private $template_version;

    function __construct()
    {
        $this->template_version = self::TEMPLATE_VERSION_DEFAULT;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Settings
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Settings
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     * @return Settings
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string 
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Settings
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set admin_email
     *
     * @param string $adminEmail
     * @return Settings
     */
    public function setAdminEmail($adminEmail)
    {
        $this->admin_email = $adminEmail;

        return $this;
    }

    /**
     * Get admin_email
     *
     * @return string 
     */
    public function getAdminEmail()
    {
        return $this->admin_email;
    }

    /**
     * Set manager_email
     *
     * @param string $managerEmail
     * @return Settings
     */
    public function setManagerEmail($managerEmail)
    {
        $this->manager_email = $managerEmail;

        return $this;
    }

    /**
     * Get manager_email
     *
     * @return string 
     */
    public function getManagerEmail()
    {
        return $this->manager_email;
    }

    /**
     * Set mailer_host
     *
     * @param string $mailerHost
     * @return Settings
     */
    public function setMailerHost($mailerHost)
    {
        $this->mailer_host = $mailerHost;

        return $this;
    }

    /**
     * Get mailer_host
     *
     * @return string 
     */
    public function getMailerHost()
    {
        return $this->mailer_host;
    }

    /**
     * Set mailer_port
     *
     * @param string $mailerPort
     * @return Settings
     */
    public function setMailerPort($mailerPort)
    {
        $this->mailer_port = $mailerPort;

        return $this;
    }

    /**
     * Get mailer_port
     *
     * @return string 
     */
    public function getMailerPort()
    {
        return $this->mailer_port;
    }

    /**
     * Set mailer_user
     *
     * @param string $mailerUser
     * @return Settings
     */
    public function setMailerUser($mailerUser)
    {
        $this->mailer_user = $mailerUser;

        return $this;
    }

    /**
     * Get mailer_user
     *
     * @return string 
     */
    public function getMailerUser()
    {
        return $this->mailer_user;
    }

    /**
     * Set mailer_password
     *
     * @param string $mailerPassword
     * @return Settings
     */
    public function setMailerPassword($mailerPassword)
    {
        $this->mailer_password = $mailerPassword;

        return $this;
    }

    /**
     * Get mailer_password
     *
     * @return string 
     */
    public function getMailerPassword()
    {
        return $this->mailer_password;
    }

    /**
     * @param string $vk_url
     */
    public function setVkUrl($vk_url)
    {
        $this->vk_url = $vk_url;
    }

    /**
     * @return string
     */
    public function getVkUrl()
    {
        return $this->vk_url;
    }

    /**
     * @param string $fb_url
     */
    public function setFbUrl($fb_url)
    {
        $this->fb_url = $fb_url;
    }

    /**
     * @return string
     */
    public function getFbUrl()
    {
        return $this->fb_url;
    }

    /**
     * @param string $google_url
     */
    public function setGoogleUrl($google_url)
    {
        $this->google_url = $google_url;
    }

    /**
     * @return string
     */
    public function getGoogleUrl()
    {
        return $this->google_url;
    }

    /**
     * @param mixed $admin_email_template
     */
    public function setAdminEmailTemplate($admin_email_template)
    {
        $this->admin_email_template = $admin_email_template;
    }

    /**
     * @return mixed
     */
    public function getAdminEmailTemplate()
    {
        return $this->admin_email_template;
    }

    /**
     * @param mixed $customer_email_template
     */
    public function setCustomerEmailTemplate($customer_email_template)
    {
        $this->customer_email_template = $customer_email_template;
    }

    /**
     * @return mixed
     */
    public function getCustomerEmailTemplate()
    {
        return $this->customer_email_template;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $manager_email_template
     */
    public function setManagerEmailTemplate($manager_email_template)
    {
        $this->manager_email_template = $manager_email_template;
    }

    /**
     * @return mixed
     */
    public function getManagerEmailTemplate()
    {
        return $this->manager_email_template;
    }

    /**
     * Set logo
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $logo
     * @return Settings
     */
    public function setLogo(Media $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set favicon
     *
     * @param \Application\Sonata\MediaBundle\Entity\Media $favicon
     * @return Settings
     */
    public function setFavicon(Media $favicon = null)
    {
        $this->favicon = $favicon;

        return $this;
    }

    /**
     * Get favicon
     *
     * @return \Application\Sonata\MediaBundle\Entity\Media
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * Set template_version
     *
     * @param integer $templateVersion
     * @return Settings
     */
    public function setTemplateVersion($templateVersion)
    {
        $this->template_version = $templateVersion;

        return $this;
    }

    /**
     * Get template_version
     *
     * @return integer 
     */
    public function getTemplateVersion()
    {
        //@TODO add versions extension
        //$template_version = isset($_GET['template_version']) ? $_GET['template_version'] : ($this->template_version ?: self::TEMPLATE_VERSION_DEFAULT);
        $template_version = self::TEMPLATE_VERSION_LEBEDEV;
        return $template_version;
    }

    /**
     * @return string
     */
    public function getTemplateName(){
        return self::$version_names[$this->getTemplateVersion()];
    }
}
