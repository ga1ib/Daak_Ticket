<?php include 'header.php'; ?>
<div class="contactpg aboutpg">
    <div class="container-lg">
        <div class="row align-items-center">
            <div class="col-md-12 text-center">
                <h2>Explore blogs</h2>
                <ul class="breadcrumb pt-2">
                    <li><a href="index.php">Home </a></li>
                    <li class="active"><i class="fa-solid fa-angle-right ps-2 pe-2"></i>Our Blogs</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- blog starts -->
<div class="blogs cp60">
    <div class="container-lg">
        <div class="about_title d-flex align-items-center justify-content-between mb-5">
            <!-- Filter by categories -->
            <h3>Filter By</h3>
            <div>
                <form method="GET" action="">
                    <select name="category_id" class="form-select form-control" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        <?php
                        // category fetch
                        $category_query = "SELECT * FROM category ORDER BY category_name ASC";
                        $category_result = mysqli_query($conn, $category_query);

                        if ($category_result && mysqli_num_rows($category_result) > 0) {
                            while ($category = mysqli_fetch_assoc($category_result)) {
                                $selected = (isset($_GET['category_id']) && $_GET['category_id'] == $category['category_id']) ? 'selected' : '';
                                echo "<option value='{$category['category_id']}' $selected>" . htmlspecialchars($category['category_name']) . "</option>";
                            }
                        }
                        ?>
                    </select>
                </form>
            </div>
        </div>
        <div class="row gy-4" id="masonry-grid">
            <?php
            // Get the selected category from query string
            $category_filter = isset($_GET['category_id']) ? intval($_GET['category_id']) : null;

            $query = "SELECT p.*, u.username, c.category_name
            FROM blog_post p
            LEFT JOIN user u ON p.user_id = u.user_id
            LEFT JOIN category c ON p.category_id = c.category_id
            WHERE p.status = 'approved'";

            if ($category_filter) {
                $query .= " AND p.category_id = $category_filter";
            }

            $query .= " ORDER BY p.created_at DESC";
            $result = mysqli_query($conn, $query);

            if ($result && mysqli_num_rows($result) > 0) {
                while ($post = mysqli_fetch_assoc($result)) {
                    $excerpt = substr($post['content'], 0, 90) . (strlen($post['content']) > 90 ? '...' : '');
                    ?>
                    <div class="col-md-4">
                        <div class="blog_box">
                            <a href="view-post.php?post_id=<?php echo $post['post_id']; ?>">
                                <img src="<?php echo htmlspecialchars($post['feature_image']); ?>" class="img-fluid"
                                    alt="<?php echo htmlspecialchars($post['title']); ?>">
                            </a>
                            <div class="post_title">
                                <div class="author_box d-flex align-items-center pt-2">
                                    <i class="lni lni-pencil-1"></i>
                                    <a href="profile.php?user_id=<?php echo $post['user_id']; ?>" class="text-decoration-none">
                                        <?php echo htmlspecialchars($post['username'] ?? 'Unknown'); ?>
                                    </a>
                                    <i class="lni lni-calendar-days ps-4"></i>
                                    <p class="ps-2"><?php echo date('d M, Y', strtotime($post['created_at'])); ?></p>
                                </div>
                                <div class="author_box category_name">
                                    <p>Category: <?php echo htmlspecialchars($post['category_name'] ?? 'Uncategorized'); ?>
                                    </p>
                                </div>
                                <div class="blog_ttle">
                                    <a href="view-post.php?post_id=<?php echo $post['post_id']; ?>">
                                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                                    </a>
                                    <p><?php echo htmlspecialchars($excerpt); ?></p>
                                </div>
                                <div class="like_box mt-2 d-flex align-items-center">
                                    <i class="lni lni-thumbs-up-3"></i>
                                    <span class="like-count ps-2">
                                        <?php
                                        $post_id = $post['post_id']; // Assuming $post['post_id'] is already available
                                        $like_query = "SELECT COUNT(*) AS like_count FROM likes WHERE post_id = '$post_id'";
                                        $like_result = mysqli_query($conn, $like_query);
                                        $like_data = mysqli_fetch_assoc($like_result);
                                        echo htmlspecialchars($like_data['like_count'] ?? 0); // if no likes then 0
                                        ?>
                                    </span>
                                    <div class="comment-count-box d-flex align-items-center ps-3 pe-3">
                                        <a href="view-post.php?post_id=<?php echo $post['post_id']; ?>#comment_section">
                                            <i class="lni lni-comment-1-text"></i></a>
                                        <span class="comment-count">
                                            <?php
                                            $post_id = $post['post_id'];
                                            $comment_query = "SELECT COUNT(*) AS comment_count FROM comment WHERE post_id = '$post_id'";
                                            $comment_result = mysqli_query($conn, $comment_query);
                                            $comment_data = mysqli_fetch_assoc($comment_result);
                                            echo htmlspecialchars($comment_data['comment_count'] ?? 0); // if no comments then 0
                                            ?>
                                        </span>
                                    </div>
                                    <!-- Share Modal Trigger -->
                                    <i class="lni lni-share-1" data-bs-toggle="modal"
                                        data-bs-target="#shareModal-<?php echo $post['post_id']; ?>"></i>
                                    <!-- share modal is in footer -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<div class="nothing_found text-center col-md-4">
                <img src="assets/uploads/published.png" class="img-fluid w-10" alt="published">
                <p class="text-center mt-4">No posts found.</p>
              </div>';
            }
            ?>
        </div>
    </div>
</div>
</div>
<!-- blog ends -->

<?php include 'footer.php'; ?>