<?php

namespace Shop\MainBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Weasty\Doctrine\Entity\AbstractEntity;

/**
 * Class Settings
 * @package Shop\MainBundle\Entity
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
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $actions_title;

    /**
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $actions_description;

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
     * @var string
     */
    private $name;

    /**
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $logo_file_name;

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
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $catalog_download_title;

    /**
     * @var string
     */
    private $why_us_title;

    /**
     * @var string
     */
    private $why_us_description;

    /**
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $proposals_title;

    /**
     * @TODO remove
     * @deprecated
     * @var string
     */
    private $proposals_description;

    /**
     * @var string
     */
    private $proposals_image_file_name;

    /**
     * @var string
     */
    private $request_title;

    /**
     * @var string
     */
    private $request_description;

    /**
     * @var string
     */
    private $benefits_title;

    /**
     * @var string
     */
    private $benefits_description;

    /**
     * @var string
     */
    private $reviews_title;

    /**
     * @var string
     */
    private $reviews_description;

    /**
     * @var string
     */
    private $how_we_title;

    /**
     * @var string
     */
    private $how_we_description;

    /**
     * @var string
     */
    private $problems_solutions_title;

    /**
     * @var string
     */
    private $problems_solutions_description;

    /**
     * @var string
     */
    private $problems_title;

    /**
     * @var string
     */
    private $solutions_title;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    protected $address;

    /**
     * @var string
     */
    protected $contacts_title;

    /**
     * @var string
     */
    protected $footer_description;

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
     * @var integer
     */
    private $template_version;

    function __construct()
    {
        $this->template_version = self::TEMPLATE_VERSION_DEFAULT;
    }

    /**
     * @param string $contacts_title
     */
    public function setContactsTitle($contacts_title)
    {
        $this->contacts_title = $contacts_title;
    }

    /**
     * @return string
     */
    public function getContactsTitle()
    {
        return $this->contacts_title;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
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
     * @return null|string
     */
    public function getLogoUrl(){
        return $this->getFileUrl($this->getLogoFilename());
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
     * Set catalog_download_title
     *
     * @param string $catalogDownloadTitle
     * @return Settings
     */
    public function setCatalogDownloadTitle($catalogDownloadTitle)
    {
        $this->catalog_download_title = $catalogDownloadTitle;

        return $this;
    }

    /**
     * Get catalog_download_title
     *
     * @return string 
     */
    public function getCatalogDownloadTitle()
    {
        return $this->catalog_download_title;
    }

    /**
     * Set why_us_title
     *
     * @param string $whyUsTitle
     * @return Settings
     */
    public function setWhyUsTitle($whyUsTitle)
    {
        $this->why_us_title = $whyUsTitle;

        return $this;
    }

    /**
     * Get why_us_title
     *
     * @return string 
     */
    public function getWhyUsTitle()
    {
        return $this->why_us_title;
    }

    /**
     * Set why_us_description
     *
     * @param string $whyUsDescription
     * @return Settings
     */
    public function setWhyUsDescription($whyUsDescription)
    {
        $this->why_us_description = $whyUsDescription;

        return $this;
    }

    /**
     * Get why_us_description
     *
     * @return string 
     */
    public function getWhyUsDescription()
    {
        return $this->why_us_description;
    }

    /**
     * Set proposals_title
     *
     * @param string $proposalsTitle
     * @return Settings
     */
    public function setProposalsTitle($proposalsTitle)
    {
        $this->proposals_title = $proposalsTitle;

        return $this;
    }

    /**
     * Get proposals_title
     *
     * @return string 
     */
    public function getProposalsTitle()
    {
        return $this->proposals_title;
    }

    /**
     * Set proposals_description
     *
     * @param string $proposalsDescription
     * @return Settings
     */
    public function setProposalsDescription($proposalsDescription)
    {
        $this->proposals_description = $proposalsDescription;

        return $this;
    }

    /**
     * Get proposals_description
     *
     * @return string 
     */
    public function getProposalsDescription()
    {
        return $this->proposals_description;
    }

    /**
     * Set proposals_image_file_name
     *
     * @param string $proposalsImgUrl
     * @return Settings
     */
    public function setProposalsImageFileName($proposalsImgUrl)
    {
        $this->proposals_image_file_name = $proposalsImgUrl;

        return $this;
    }

    /**
     * Get proposals_image_file_name
     *
     * @return string 
     */
    public function getProposalsImageFileName()
    {
        return $this->proposals_image_file_name;
    }

    public function getProposalsImage(){
        return $this->getFile('proposals_image_file_name');
    }

    public function setProposalsImage($file = null){
        $this->setFile('proposals_image_file_name', $file);
    }

    public function getProposalsImageUrl(){
        return $this->getFileUrl($this->getProposalsImageFileName());
    }

    /**
     * Set request_title
     *
     * @param string $requestTitle
     * @return Settings
     */
    public function setRequestTitle($requestTitle)
    {
        $this->request_title = $requestTitle;

        return $this;
    }

    /**
     * Get request_title
     *
     * @return string 
     */
    public function getRequestTitle()
    {
        return $this->request_title;
    }

    /**
     * Set request_description
     *
     * @param string $requestDescription
     * @return Settings
     */
    public function setRequestDescription($requestDescription)
    {
        $this->request_description = $requestDescription;

        return $this;
    }

    /**
     * Get request_description
     *
     * @return string 
     */
    public function getRequestDescription()
    {
        return $this->request_description;
    }

    /**
     * Set benefits_title
     *
     * @param string $benefitsTitle
     * @return Settings
     */
    public function setBenefitsTitle($benefitsTitle)
    {
        $this->benefits_title = $benefitsTitle;

        return $this;
    }

    /**
     * Get benefits_title
     *
     * @return string 
     */
    public function getBenefitsTitle()
    {
        return $this->benefits_title;
    }

    /**
     * Set benefits_description
     *
     * @param string $benefitsDescription
     * @return Settings
     */
    public function setBenefitsDescription($benefitsDescription)
    {
        $this->benefits_description = $benefitsDescription;

        return $this;
    }

    /**
     * Get benefits_description
     *
     * @return string 
     */
    public function getBenefitsDescription()
    {
        return $this->benefits_description;
    }

    /**
     * Set reviews_title
     *
     * @param string $reviewsTitle
     * @return Settings
     */
    public function setReviewsTitle($reviewsTitle)
    {
        $this->reviews_title = $reviewsTitle;

        return $this;
    }

    /**
     * Get reviews_title
     *
     * @return string 
     */
    public function getReviewsTitle()
    {
        return $this->reviews_title;
    }

    /**
     * Set reviews_description
     *
     * @param string $reviewsDescription
     * @return Settings
     */
    public function setReviewsDescription($reviewsDescription)
    {
        $this->reviews_description = $reviewsDescription;

        return $this;
    }

    /**
     * Get reviews_description
     *
     * @return string 
     */
    public function getReviewsDescription()
    {
        return $this->reviews_description;
    }

    /**
     * Set how_we_title
     *
     * @param string $howWeTitle
     * @return Settings
     */
    public function setHowWeTitle($howWeTitle)
    {
        $this->how_we_title = $howWeTitle;

        return $this;
    }

    /**
     * Get how_we_title
     *
     * @return string 
     */
    public function getHowWeTitle()
    {
        return $this->how_we_title;
    }

    /**
     * Set how_we_description
     *
     * @param string $howWeDescription
     * @return Settings
     */
    public function setHowWeDescription($howWeDescription)
    {
        $this->how_we_description = $howWeDescription;

        return $this;
    }

    /**
     * Get how_we_description
     *
     * @return string 
     */
    public function getHowWeDescription()
    {
        return $this->how_we_description;
    }

    /**
     * Set problems_solutions_title
     *
     * @param string $problemsSolutionsTitle
     * @return Settings
     */
    public function setProblemsSolutionsTitle($problemsSolutionsTitle)
    {
        $this->problems_solutions_title = $problemsSolutionsTitle;

        return $this;
    }

    /**
     * Get problems_solutions_title
     *
     * @return string 
     */
    public function getProblemsSolutionsTitle()
    {
        return $this->problems_solutions_title;
    }

    /**
     * Set problems_solutions_description
     *
     * @param string $problemsSolutionsDescription
     * @return Settings
     */
    public function setProblemsSolutionsDescription($problemsSolutionsDescription)
    {
        $this->problems_solutions_description = $problemsSolutionsDescription;

        return $this;
    }

    /**
     * Get problems_solutions_description
     *
     * @return string 
     */
    public function getProblemsSolutionsDescription()
    {
        return $this->problems_solutions_description;
    }

    /**
     * Set problems_title
     *
     * @param string $problemsTitle
     * @return Settings
     */
    public function setProblemsTitle($problemsTitle)
    {
        $this->problems_title = $problemsTitle;

        return $this;
    }

    /**
     * Get problems_title
     *
     * @return string 
     */
    public function getProblemsTitle()
    {
        return $this->problems_title;
    }

    /**
     * Set solutions_title
     *
     * @param string $solutionsTitle
     * @return Settings
     */
    public function setSolutionsTitle($solutionsTitle)
    {
        $this->solutions_title = $solutionsTitle;

        return $this;
    }

    /**
     * Get solutions_title
     *
     * @return string 
     */
    public function getSolutionsTitle()
    {
        return $this->solutions_title;
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

    protected $catalog_file_name;

    /**
     * @param mixed $catalog_file_name
     */
    public function setCatalogFileName($catalog_file_name)
    {
        $this->catalog_file_name = $catalog_file_name;
    }

    /**
     * @return mixed
     */
    public function getCatalogFileName()
    {
        return $this->catalog_file_name;
    }

    public function getCatalogFile(){
        return $this->getFile('catalog_file_name');
    }

    public function setCatalogFile($file = null){
        $this->setFile('catalog_file_name', $file);
    }

    public function getCatalogFileUrl(){
        return $this->getFileUrl($this->getCatalogFileName());
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
     * @param string $footer_description
     */
    public function setFooterDescription($footer_description)
    {
        $this->footer_description = $footer_description;
    }

    /**
     * @return string
     */
    public function getFooterDescription()
    {
        return $this->footer_description;
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

    protected $main_link_text;

    protected $why_link_text;

    protected $more_link_text;

    protected $reviews_link_text;

    protected $where_link_text;

    /**
     * @param mixed $main_link_text
     */
    public function setMainLinkText($main_link_text)
    {
        $this->main_link_text = $main_link_text;
    }

    /**
     * @return mixed
     */
    public function getMainLinkText()
    {
        return $this->main_link_text;
    }

    /**
     * @param mixed $more_link_text
     */
    public function setMoreLinkText($more_link_text)
    {
        $this->more_link_text = $more_link_text;
    }

    /**
     * @return mixed
     */
    public function getMoreLinkText()
    {
        return $this->more_link_text;
    }

    /**
     * @param mixed $reviews_link_text
     */
    public function setReviewsLinkText($reviews_link_text)
    {
        $this->reviews_link_text = $reviews_link_text;
    }

    /**
     * @return mixed
     */
    public function getReviewsLinkText()
    {
        return $this->reviews_link_text;
    }

    /**
     * @param mixed $where_link_text
     */
    public function setWhereLinkText($where_link_text)
    {
        $this->where_link_text = $where_link_text;
    }

    /**
     * @return mixed
     */
    public function getWhereLinkText()
    {
        return $this->where_link_text;
    }

    /**
     * @param mixed $why_link_text
     */
    public function setWhyLinkText($why_link_text)
    {
        $this->why_link_text = $why_link_text;
    }

    /**
     * @return mixed
     */
    public function getWhyLinkText()
    {
        return $this->why_link_text;
    }

    protected $request_timer_end_date;

    /**
     * @param mixed $request_timer_end_date
     */
    public function setRequestTimerEndDate($request_timer_end_date)
    {
        $this->request_timer_end_date = $request_timer_end_date;
    }

    /**
     * @return mixed
     */
    public function getRequestTimerEndDate()
    {
        return $this->request_timer_end_date;
    }

    protected $email;

    protected $customer_email_template;

    protected $admin_email_template;

    protected $manager_email_template;

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
     * Set logo_file_name
     * @TODO remove
     * @deprecated
     * @param string $logoFileName
     * @return Settings
     */
    public function setLogoFileName($logoFileName)
    {
        $this->logo_file_name = $logoFileName;

        return $this;
    }

    /**
     * Get logo_file_name
     * @TODO remove
     * @deprecated
     * @return string
     */
    public function getLogoFileName()
    {
        return $this->logo_file_name;
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
     * @return string
     */
    public function getActionsDescription()
    {
        return $this->actions_description;
    }

    /**
     * @return string
     */
    public function getActionsTitle()
    {
        return $this->actions_title;
    }

    /**
     * @param string $actions_title
     */
    public function setActionsTitle($actions_title)
    {
        $this->actions_title = $actions_title;
    }

    /**
     * @param string $actions_description
     */
    public function setActionsDescription($actions_description)
    {
        $this->actions_description = $actions_description;
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

    /**
     * @var string
     */
    private $manufacturers_title;

    /**
     * @var string
     */
    private $manufacturers_description;


    /**
     * Set manufacturers_title
     *
     * @param string $manufacturersTitle
     * @return Settings
     */
    public function setManufacturersTitle($manufacturersTitle)
    {
        $this->manufacturers_title = $manufacturersTitle;

        return $this;
    }

    /**
     * Get manufacturers_title
     *
     * @return string 
     */
    public function getManufacturersTitle()
    {
        return $this->manufacturers_title;
    }

    /**
     * Set manufacturers_description
     *
     * @param string $manufacturersDescription
     * @return Settings
     */
    public function setManufacturersDescription($manufacturersDescription)
    {
        $this->manufacturers_description = $manufacturersDescription;

        return $this;
    }

    /**
     * Get manufacturers_description
     *
     * @return string 
     */
    public function getManufacturersDescription()
    {
        return $this->manufacturers_description;
    }
}
