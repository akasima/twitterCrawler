# twitterCrawler

이 코드는 Twitter 에서 타임라인을 크롤링하여 트윗을 저장하고 저장한 트윗을 인기순서에 따라 보여줍니다.

### 참고
* database 에 있는 sql 파일로 테이블을 만들고
* config.php 의 정보를 설정하세요.
* twitter consumerKey, accessToken 은 변경되어 사용 할 수 없을 것입니다.
 > 트위터에서 새로 발급 받아서 사용하시기 바랍니다.

step1, step2, step3 은 모두 같은 기능입니다. 

step1을 기준으로 살펴보면 

domain/step1/index.php, domain/step2/week.php 두개의 페이지가 있으며 
index.php 는 최근 1일 동안 가장 인기있는 트윗을 순위로 노출 시키고 
week.php 는 최근 1주일 동안 가장 인기있는 트윗을 노출 시킵니다.

![ScreenShot](https://github.com/akasima/twitterCrawler/blob/master/today.png)

![ScreenShot](https://github.com/akasima/twitterCrawler/blob/master/weekly.png)
