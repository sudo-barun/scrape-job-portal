<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>Scrape Job Portals</title>
    <meta name="title" content="Scrape Job Portals">
    <meta name="description" content="All Jobs In One Place">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://scrape-job-portal.herokuapp.com">
    <meta property="og:title" content="Scrape Job Portals">
    <meta property="og:description" content="All Jobs In One Place">
    <meta property="og:image" content="http://barunkharel.info.np/cover.png">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="https://scrape-job-portal.herokuapp.com">
    <meta property="twitter:title" content="Scrape Job Portals">
    <meta property="twitter:description" content="All Jobs In One Place">
    <meta property="twitter:image" content="http://barunkharel.info.np/cover.png">

    <link rel="stylesheet" href="css/app.css" >
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <div class="h1 text-center">IT jobs in one place</div>
    </div>
</div>

<div class="container">

    <div class="jump-links">
        Jump to:
        <?php foreach ($portalsData as $portal => $jobPortalData): ?>
            <div><a href="#<?= $portal ?>"><?= $jobPortalData['name'] ?></a></div>
        <?php endforeach ?>
    </div>

    <form autocomplete="off">
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text">Search Terms: </span>
            </div>
            <input name="q" value="<?= $q ?>" autofocus placeholder="e.g. php intern lalitpur" class="form-control">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span></button>
            </div>
        </div>
    </form>

    <div class="mt-5"></div>

    <?php foreach ($portalsData as $portal => $jobData): $jobs = $jobData['jobs']; ?>
        <div id="<?= $portal ?>" class="card mb-5">
            <div class="card-header">
                <h5 class="job-portal">
                    <span class="title-wrapper" style="font-size: 1.2em">
                        <img src="<?= $jobData['logo'] ?>" height="16" style="height: 1em">
                        <?= strtoupper($portal) ?>
                    </span>
                    <span class="badge badge-secondary badge-pill" title="Total: <?= count($jobs) ?>"><?= count($jobs) ?></span>
                </h5>
            </div>
            <div class="job-list list-group list-group-flush">
                <?php foreach ($jobs as $i => $job): ?>
                    <div class="job-item list-group-item">
                        <h6 class="job-title">
                            <?= $i + 1 ?>.
                            <a href="<?= $job['link'] ?>" target="_blank" rel="nofollow noreferrer noopener" class="job-link">
                                <?= $job['title'] ?>
                            </a>
                            <span class="badge badge-pill badge-light"><?= $job['type'] ?></span>
                        </h6>
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="job-company" title="Company">
                                    <span class="fa fa-building"></span>
                                    <?= $job['company']['title'] ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div title="Address">
                                    <span class="fa fa-map-marker"></span>
                                    <?= $job['address'] ?? '<abbr title="Not Available" class="small">N.A.</abbr>' ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div title="Salary">
                                    <span class="fa fa-money-bill-wave"></span>
                                    <?= $job['salary'] ?? '<abbr title="Not Available" class="small">N.A.</abbr>' ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div title="Skills">
                                    <span class="fa fa-tasks"></span>
                                    <?= isset($job['skills'])
                                        ? join('&nbsp;', array_map(function ($s) {
                                        return '<span class="badge badge-secondary">' . $s . '</span>';
                                        }, $job['skills']))
                                        : '<abbr title="Not Available" class="small">N.A.</abbr>'
                                    ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div title="Posted On">
                                    <span class="fa fa-folder-plus"></span>
                                    <?= $job['posted_on'] ?? '<abbr title="Not Available" class="small">N.A.</abbr>' ?>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div title="Expires On">
                                    <span class="fa fa-ban"></span>
                                    <?= $job['expires_on'] ?? '<abbr title="Not Available" class="small">N.A.</abbr>' ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach ?>
            </div>
        </div>
    <?php endforeach ?>

    <?php if ($lastAttemptMap): ?>
    <div>
        Latest scrapes:
        <?php foreach ($lastAttemptMap as $datetime => $jobPortals): ?>
            <div>
                <?= $datetime ?> :
                <?= join(', ', array_map('strtoupper', $jobPortals)) ?>
            </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>

</div>

<div class="page-bottom-gap"></div>

<nav class="navbar fixed-bottom navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <div class="mx-auto navbar-text">
            Contribute on <a href="https://github.com/sudo-barun/scrape-job-portal" target="_blank">GitHub <span class="fab fa-github"></span></a>
        </div>
    </div>
</nav>

<script src="js/app.js"></script>

</body>
</html>
