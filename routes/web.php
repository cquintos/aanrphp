<?php
use Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\Mail;
use Illuminate\Auth\Events\Registered;
use App\Jobs\QuarterlyMailJob;
use Illuminate\Auth\Events\Verified;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Auth::routes();

Route::get('/', 'PagesController@getLandingPage')->name('getLandingPage');
Route::get('about', 'PagesController@aboutUs')->name('aboutUs');
Route::get('aanr/about', 'PagesController@AANRAboutPage')->name('AANRAboutPage');
Route::get('redirect/community', 'PagesController@communityPage')->name('goToCommunity');
Route::get('analytics/search', 'PagesController@searchAnalytics')->name('searchAnalytics');
Route::get('analytics/searchWithFilter', 'PagesController@searchAnalyticsWithFilter')->name('searchAnalyticsWithFilter');
Route::get('countries', [CountryController::class, 'index']);
Route::post('signup/createUser', 'UsersController@createUser')->name('createUser');

Route::group(['middleware' => ['auth']], function() {
    Route::get('/email/verify', 'VerificationController@show')->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', 'VerificationController@verify')->name('verification.verify')->middleware(['signed']);
    Route::post('/email/resend', 'VerificationController@resend')->name('verification.resend');
    
    Route::group(['middleware' => ['verified']], function() {
        Route::get('search', 'PagesController@search')->name('search');
        Route::get('unit/about', 'PagesController@unitAboutPage')->name('unitAboutPage');
        Route::get('usefulLinks', 'PagesController@usefulLinks')->name('usefulLinks');
        Route::get('agrisyunaryo', 'PagesController@agrisyunaryo')->name('agrisyunaryo');
        Route::get('pcaarrd/about', 'PagesController@PCAARRDAboutPage')->name('PCAARRDAboutPage');
        Route::get('consortia/about', 'PagesController@consortiaAboutPage')->name('consortiaAboutPage');
        Route::get('consortia/landing', 'PagesController@consortiaLandingPage')->name('consortiaLandingPage');
        Route::get('aanr-industry-profile', 'PagesController@industryProfileView')->name('industryProfileView');
        Route::get('dashboard/userDashboard', 'PagesController@userDashboard')->name('userDashboard');
        Route::get('/unsubscribe', 'UsersController@unsubscribeUser')->name('unsubUser');
        Route::get('analytics/search/save', 'PagesController@saveAnalytics')->name('saveAnalytics');
        Route::post('headlines/{id}/editUser', 'UsersController@editUser')->name('editUser');
        Route::get('agrisyunaryo/search', 'PagesController@agrisyunaryoSearch')->name('agrisyunaryoSearch');
        Route::get('logs/consortia/download', 'LogsController@exportConsortiaLogs')->name('exportConsortiaLogs');

        
        Route::group(['middleware' => ['consortia_admin']], function() {
            Route::post('manage/updateConsortiaBanner', ['uses' => 'LandingPageElementsController@updateConsortiaBanner', 'as' => 'landing.updateConsortiaBanner']);
            Route::post('headlines/addConsortia', 'ConsortiaController@addConsortia')->name('addConsortia');
            Route::post('headlines/addConsortiaMember', 'ConsortiaMembersController@addConsortiaMember')->name('addConsortiaMember');
            Route::post('headlines/{id}/editConsortia', 'ConsortiaController@editConsortia')->name('editConsortia');
            Route::post('headlines/{id}/editConsortiaBanner', 'ConsortiaController@editConsortiaBanner')->name('editConsortiaBanner');
            Route::post('headlines/{id}/editConsortiaLandingPageBanner', 'ConsortiaController@editConsortiaLandingPageBanner')->name('editConsortiaLandingPageBanner');
            Route::post('headlines/{id}/editConsortiaDetails', 'ConsortiaController@editConsortiaDetails')->name('editConsortiaDetails');
            Route::post('headlines/{id}/editConsortiaMember', 'ConsortiaMembersController@editConsortiaMember')->name('editConsortiaMember');
            Route::post('headlines/{id}/editConsortiaMemberBanner', 'ConsortiaMembersController@editConsortiaMemberBanner')->name('editConsortiaMemberBanner');
            Route::post('headlines/{id}/editConsortiaMemberDetails', 'ConsortiaMembersController@editConsortiaMemberDetails')->name('editConsortiaMemberDetails');
            Route::post('headlines/{id}/editArtifact', 'ArtifactAANRController@editArtifact')->name('editArtifact');
            Route::post('consortia/{id}/editLatestAANRSection', 'ConsortiaController@editConsortiaLatestAANRSection')->name('editConsortiaLatestAANRSection');
            Route::post('consortia/{id}/editFeaturedPublicationsSection', 'ConsortiaController@editConsortiaFeaturedPublicationsSection')->name('editConsortiaFeaturedPublicationsSection');
            Route::post('consortia/{id}/editFeaturedVideosSection', 'ConsortiaController@editConsortiaFeaturedVideosSection')->name('editConsortiaFeaturedVideosSection');
            Route::post('consortia/{id}/editConsortiaMembersSection', 'ConsortiaController@editConsortiaConsortiaMembersSection')->name('editConsortiaConsortiaMembersSection');
            Route::post('headlines/uploadArtifactAPI', 'ArtifactAANRController@uploadArtifactAPI')->name('uploadArtifactAPI');
            Route::post('headlines/uploadArtifactForm', 'ArtifactAANRController@uploadArtifactForm')->name('uploadArtifactForm');
            Route::post('headlines/uploadArtifactCSV', 'ArtifactAANRController@uploadArtifactCSV')->name('uploadArtifactCSV');
            Route::post('headlines/uploadPDFArtifact', 'ArtifactAANRController@uploadPDFArtifact')->name('uploadPDFArtifact');
            Route::post('dashboard/admin/fetchConsortiaMemberDependent', 'ArtifactAANRController@fetchConsortiaMemberDependent')->name('fetchConsortiaMemberDependent');
            Route::post('dashboard/admin/fetchContentSubtypeDependent', 'ArtifactAANRController@fetchContentSubtypeDependent')->name('fetchContentSubtypeDependent');
            Route::post('dashboard/admin/fetchCommodityDependent', 'ArtifactAANRController@fetchCommodityDependent')->name('fetchCommodityDependent');
            Route::get('dashboard/admin/artifactModalView', 'ArtifactAANRController@artifactModalView')->name('artifactModalView');
            Route::get('dashboard/admin/fetchCommoditySubtypeDependent', 'ArtifactAANRController@fetchCommoditySubtypeDependent')->name('fetchCommoditySubtypeDependent');
            Route::get('dashboard/admin/artifact/{id}/edit', 'PagesController@artifactEdit')->name('artifactEdit');
            Route::get('dashboard/admin/artifact/{id}/view', 'PagesController@artifactView')->name('artifactView');
            Route::get('dashboard/admin/artifact/upload', 'PagesController@artifactUpload')->name('artifactUpload');
            Route::delete('headlines/{id}/deleteConsortiaMember', 'ConsortiaMembersController@deleteConsortiaMember')->name('deleteConsortiaMember');
            Route::delete('headlines/{id}/deleteConsortia', 'ConsortiaController@deleteConsortia')->name('deleteConsortia');
            Route::delete('headlines/{id}/deleteSingleArtifact', 'ArtifactAANRController@deleteSingleArtifact')->name('deleteSingleArtifact');
            Route::delete('headlines/deleteArtifact', 'ArtifactAANRController@deleteArtifact')->name('deleteArtifact');
            
            Route::group(['middleware' => ['admin']], function() {
                Route::post('headlines/{id}/setUserAdmin', 'ConsortiaController@setUserAdmin')->name('setUserAdmin');
                Route::post('manage/addIndustry', 'IndustriesController@addIndustry')->name('addIndustry');
                Route::post('manage/updateTopBanner', ['uses' => 'LandingPageElementsController@updateTopBanner', 'as' => 'landing.updateTopBanner']);
                Route::post('manage/updateHeaderLogo', ['uses' => 'LandingPageElementsController@updateHeaderLogo', 'as' => 'landing.updateHeaderLogo']);
                Route::post('manage/updateLandingPageItems', ['uses' => 'LandingPageElementsController@updateLandingPageItems', 'as' => 'pages.updateLandingPageItems']);
                Route::post('manage/updateLandingPageViews', ['uses' => 'LandingPageElementsController@updateLandingPageViews', 'as' => 'l.updateLandingPageViews']);
                Route::post('manage/editIndustryProfileSection', 'LandingPageElementsController@editIndustryProfileSection')->name('editIndustryProfileSection');
                Route::post('manage/editLatestAANRSection', 'LandingPageElementsController@editLatestAANRSection')->name('editLatestAANRSection');
                Route::post('manage/editUserTypeRecommendationSection', 'LandingPageElementsController@editUserTypeRecommendationSection')->name('editUserTypeRecommendationSection');
                Route::post('manage/editFeaturedPublicationsSection', 'LandingPageElementsController@editFeaturedPublicationsSection')->name('editFeaturedPublicationsSection');
                Route::post('manage/editFeaturedVideosSection', 'LandingPageElementsController@editFeaturedVideosSection')->name('editFeaturedVideosSection');
                Route::post('manage/editRecommendedForYouSection', 'LandingPageElementsController@editRecommendedForYouSection')->name('editRecommendedForYouSection');
                Route::post('manage/editConsortiaMembersSection', 'LandingPageElementsController@editConsortiaMembersSection')->name('editConsortiaMembersSection');
                Route::post('manage/editAgrisyunaryoSearchBanner', 'LandingPageElementsController@editAgrisyunaryoSearchBanner')->name('editAgrisyunaryoSearchBanner');
                Route::post('manage/editIndustryProfile', 'LandingPageElementsController@editIndustryProfile')->name('editIndustryProfile');
                Route::post('manage/editFooterInfo', 'LandingPageElementsController@editFooterInfo')->name('editFooterInfo');
                Route::post('manage/editUsefulLinks', 'LandingPageElementsController@editUsefulLinks')->name('editUsefulLinks');
                Route::post('manage/{id}/editIndustry', 'IndustriesController@editIndustry')->name('editIndustry');
                Route::post('headlines/addSocial', 'SocialMediaStickyController@addSocial')->name('addSocial');
                Route::post('headlines/addHeaderLink', 'HeaderLinksController@addHeaderLink')->name('addHeaderLink');
                Route::post('headlines/addHeadline', 'HeadlinesController@addHeadline')->name('addHeadline');
                Route::post('headlines/addFooterLink', 'FooterLinksController@addFooterLink')->name('addFooterLink');
                Route::post('headlines/addAgrisyunaryo', 'AgrisyunaryosController@addAgrisyunaryo')->name('addAgrisyunaryo');
                Route::post('headlines/addSlider', 'LandingPageSlidersController@addSlider')->name('addSlider');
                Route::post('headlines/addAdvertisement', 'AdvertisementsController@addAdvertisement')->name('addAdvertisement');
                Route::post('headlines/addAnnouncement', 'AnnouncementsController@addAnnouncement')->name('addAnnouncement');
                Route::post('headlines/addAgenda', 'AgendasController@addAgenda')->name('addAgenda');
                Route::post('headlines/addView', 'ArtifactAANRController@addView')->name('addView');
                Route::post('headlines/createArtifactViewLog', 'ArtifactAANRViewsController@createArtifactViewLog')->name('createArtifactViewLog');
                Route::post('headlines/createISPViewLog', 'ISPViewsController@createISPViewLog')->name('createISPViewLog');
                Route::post('headlines/createCommodityViewLog', 'CommodityViewsController@createCommodityViewLog')->name('createCommodityViewLog');
                Route::post('headlines/addContent', 'ContentController@addContent')->name('addContent');
                Route::post('headlines/addContentSubtype', 'ContentSubtypesController@addContentSubtype')->name('addContentSubtype');
                Route::post('headlines/addContributor', 'ContributorsController@addContributor')->name('addContributor');
                Route::post('headlines/addISPIndustryID', 'ArtifactAANRController@addISPIndustryID')->name('addISPIndustryID');
                Route::post('headlines/addSector', 'SectorsController@addSector')->name('addSector');
                Route::post('headlines/addISP', 'ISPController@addISP')->name('addISP');
                Route::post('headlines/addCommodity', 'CommoditiesController@add')->name('addCommodity');
                Route::post('headlines/addSubscriber', 'SubscribersController@addSubscriber')->name('addSubscriber');
                Route::post('headlines/addAPIEntry', 'APIEntriesController@addAPIEntry')->name('addAPIEntry');
                Route::post('headlines/{id}/editSocial', 'SocialMediaStickyController@editSocial')->name('editSocial');
                Route::post('headlines/{id}/editHeaderLink', 'HeaderLinksController@editHeaderLink')->name('editHeaderLink');
                Route::post('headlines/{id}/editFooterLink', 'FooterLinksController@editFooterLink')->name('editFooterLink');
                Route::post('headlines/{id}/editAANRPage', 'AANRPageController@editAANRPage')->name('editAANRPage');
                Route::post('headlines/{id}/editAANRPageBanner', 'AANRPageController@editAANRPageBanner')->name('editAANRPageBanner');
                Route::post('headlines/{id}/editAANRPageDetails', 'AANRPageController@editAANRPageDetails')->name('editAANRPageDetails');
                Route::post('headlines/{id}/editPCAARRDPage', 'PCAARRDPageController@editPCAARRDPage')->name('editPCAARRDPage');
                Route::post('headlines/{id}/editPCAARRDPageBanner', 'PCAARRDPageController@editPCAARRDPageBanner')->name('editPCAARRDPageBanner');
                Route::post('headlines/{id}/editPCAARRDPageDetails', 'PCAARRDPageController@editPCAARRDPageDetails')->name('editPCAARRDPageDetails');
                Route::post('headlines/{id}/editHeadline', 'HeadlinesController@editHeadline')->name('editHeadline');
                Route::post('headlines/{id}/editAgrisyunaryo', 'AgrisyunaryosController@editAgrisyunaryo')->name('editAgrisyunaryo');
                Route::post('headlines/{id}/editSlider', 'LandingPageSlidersController@editSlider')->name('editSlider');
                Route::post('headlines/{id}/deleteUser', 'UsersController@deleteUser')->name('deleteUser');
                Route::post('headlines/{id}/sendConsortiaAdminRequest', 'UsersController@sendConsortiaAdminRequest')->name('sendConsortiaAdminRequest');
                Route::post('headlines/{id}/consortiaAdminRequestApprove', 'UsersController@consortiaAdminRequestApprove')->name('consortiaAdminRequestApprove');
                Route::post('headlines/{id}/consortiaAdminRequestDecline', 'UsersController@consortiaAdminRequestDecline')->name('consortiaAdminRequestDecline');
                Route::post('headlines/{id}/editAgenda', 'AgendasController@editAgenda')->name('editAgenda');
                Route::post('headlines/{id}/editAdvertisement', 'AdvertisementsController@editAdvertisement')->name('editAdvertisement');
                Route::post('headlines/{id}/editAnnouncement', 'AnnouncementsController@editAnnouncement')->name('editAnnouncement');
                Route::post('headlines/{id}/editContent', 'ContentController@editContent')->name('editContent');
                Route::post('headlines/{id}/editContentSubtype', 'ContentSubtypesController@editContentSubtype')->name('editContentSubtype');
                Route::post('headlines/{id}/editContributor', 'ContributorsController@editContributor')->name('editContributor');
                Route::post('headlines/{id}/editISP', 'ISPController@editISP')->name('editISP');
                Route::post('headlines/{id}/editSector', 'SectorsController@editSector')->name('editSector');
                Route::post('headlines/{id}/editCommodity', 'CommoditiesController@edit')->name('editCommodity');
                Route::post('headlines/{id}/editSubscriber', 'SubscribersController@editSubscriber')->name('editSubscriber');
                Route::post('ckeditor/upload', 'CKEditorController@store')->name('ckeditor.upload');
                Route::post('headlines/{id}/editAPIEntry', 'APIEntriesController@editAPIEntry')->name('editAPIEntry');
                Route::get('dashboard/admin/commodity/{id}/edit', 'CommoditiesController@editPage')->name('editCommodityPage');
                Route::get('dashboard/admin/commodity/add', 'CommoditiesController@addPage')->name('addCommodityPage');
                Route::get('logs/download', 'LogsController@exportLogs')->name('exportLogs');
                Route::get('/manage', 'PagesController@getManagePage')->name('manage');
                Route::get('dashboard/admin', 'PagesController@dashboardAdmin')->name('dashboardAdmin');
                Route::delete('headlines/deleteAgrisyunaryo', 'AgrisyunaryosController@deleteAgrisyunaryo')->name('deleteAgrisyunaryo');
                Route::delete('headlines/{id}/deleteFooterLink', 'FooterLinksController@deleteFooterLink')->name('deleteFooterLink');
                Route::delete('headlines/{id}/deleteHeaderLink', 'HeaderLinksController@deleteHeaderLink')->name('deleteHeaderLink');
                Route::delete('headlines/{id}/deleteHeadline', 'HeadlinesController@deleteHeadline')->name('deleteHeadline');
                Route::delete('headlines/{id}/deleteSlider', 'LandingPageSlidersController@deleteSlider')->name('deleteSlider');
                Route::delete('headlines/{id}/deleteAdvertisement', 'AdvertisementsController@deleteAdvertisement')->name('deleteAdvertisement');
                Route::delete('headlines/{id}/deleteAgenda', 'AgendasController@deleteAgenda')->name('deleteAgenda');
                Route::delete('headlines/{id}/deleteAnnouncement', 'AnnouncementsController@deleteAnnouncement')->name('deleteAnnouncement');
                Route::delete('headlines/{id}/deleteSector', 'SectorsController@deleteSector')->name('deleteSector');
                Route::delete('headlines/{id}/deleteCommodity', 'CommoditiesController@delete')->name('deleteCommodity');
                Route::delete('headlines/{id}/deleteSubscriber', 'SubscribersController@deleteSubscriber')->name('deleteSubscriber');
                Route::delete('headlines/{id}/deleteAPIEntry', 'APIEntriesController@deleteAPIEntry')->name('deleteAPIEntry');
                Route::delete('headlines/{id}/deleteContent', 'ContentController@deleteContent')->name('deleteContent');
                Route::delete('headlines/{id}/deleteContentSubtype', 'ContentSubtypesController@deleteContentSubtype')->name('deleteContentSubtype');
                Route::delete('headlines/{id}/deleteContributor', 'ContributorsController@deleteContributor')->name('deleteContributor');
                Route::delete('headlines/{id}/deleteISP', 'ISPController@deleteISP')->name('deleteISP');
                Route::delete('manage/{id}/deleteIndustry', 'IndustriesController@deleteIndustry')->name('deleteIndustry');
            });
        });
    });
});

// Route::get('/test', function(){
//     event(new Verified(auth()->user()));
//     // new QuarterlyMailJob();
// });
// Route::get('admin/create', 'UsersController@createAdmin')->name('createAdmin');
// Route::get('oauth', 'PagesController@oauthPage')->name('getOauth');