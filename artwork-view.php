<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$userName = $_SESSION["full_name"];

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Artwork | MIRA</title>

    <link
        rel="stylesheet"
        href="style.css"
    >

</head>


<body class="artwork-view-page">


<main class="artwork-view-container">


    <!-- BACK -->

    <a
        href="user-home.php"
        class="artwork-back"
    >
        ←
    </a>


    <!-- MAIN CARD -->

    <section class="artwork-detail-card">


        <!-- LEFT : ARTWORK -->

        <div class="artwork-detail-image">

            <img
                src="./Assets/painting1.jpg"
                alt="Artwork"
            >

        </div>



        <!-- RIGHT : INFORMATION -->

        <div class="artwork-detail-info">


            <!-- ACTION BUTTONS -->

            <div class="artwork-top-actions">


                <button
                    type="button"
                    class="artwork-action"
                    id="likeButton"
                >

                    ♡

                    <span id="likeCount">
                        120
                    </span>

                </button>


                <button
                    type="button"
                    class="artwork-action"
                >
                    💬
                </button>


                <button
                    type="button"
                    class="artwork-action"
                >
                    ↻
                </button>


                <button
                    type="button"
                    class="artwork-action"
                >
                    ↗
                </button>


                <button
                    type="button"
                    class="artwork-save"
                >
                    Save
                </button>


            </div>



            <!-- ARTIST -->

            <div class="artwork-artist">


                <div class="artwork-artist-avatar">

                    S

                </div>


                <div>

                    <strong>
                        Student Artist
                    </strong>

                    <p>
                        Painting
                    </p>

                </div>


                <button
                    type="button"
                    class="follow-button"
                >

                    Follow

                </button>


            </div>



            <!-- ARTWORK INFO -->

            <div class="artwork-description">

                <h1>
                    Colours of Nature
                </h1>

                <p>
                    A creative student artwork inspired by
                    nature, colour and imagination.
                </p>

            </div>



            <!-- COMMENTS -->

            <div class="artwork-comments">


                <h3>
                    Comments
                </h3>


                <!-- COMMENT -->

                <div class="comment-item">


                    <div class="comment-avatar">

                        D

                    </div>


                    <div>

                        <strong>
                            Dumindu
                        </strong>

                        <p>
                            Amazing artwork! ❤️
                        </p>

                    </div>


                </div>


                <!-- COMMENT -->

                <div class="comment-item">


                    <div class="comment-avatar">

                        O

                    </div>


                    <div>

                        <strong>
                            Oshini
                        </strong>

                        <p>
                            Love the colours!
                        </p>

                    </div>


                </div>


            </div>



            <!-- ADD COMMENT -->

            <form
                class="add-comment"
                id="commentForm"
            >

                <input
                    type="text"
                    id="commentInput"
                    placeholder="Add a comment..."
                    required
                >

                <button type="submit">
                    Send
                </button>

            </form>


        </div>


    </section>


</main>


<script>

const likeButton =
    document.getElementById("likeButton");

const likeCount =
    document.getElementById("likeCount");


let liked = false;
let likes = 120;


likeButton.addEventListener("click", function () {

    liked = !liked;

    if (liked) {

        likes++;

        likeButton.classList.add("liked");

        likeButton.firstChild.textContent = "♥ ";

    } else {

        likes--;

        likeButton.classList.remove("liked");

        likeButton.firstChild.textContent = "♡ ";

    }

    likeCount.textContent = likes;

});


const commentForm =
    document.getElementById("commentForm");

const commentInput =
    document.getElementById("commentInput");

const comments =
    document.querySelector(".artwork-comments");


commentForm.addEventListener("submit", function (event) {

    event.preventDefault();

    const text =
        commentInput.value.trim();

    if (text === "") {
        return;
    }


    const comment =
        document.createElement("div");

    comment.className = "comment-item";


    comment.innerHTML = `

        <div class="comment-avatar">

            <?php
                echo strtoupper(
                    substr($userName, 0, 1)
                );
            ?>

        </div>

        <div>

            <strong>
                <?php echo htmlspecialchars($userName); ?>
            </strong>

            <p></p>

        </div>

    `;


    comment.querySelector("p").textContent =
        text;


    comments.appendChild(comment);

    commentInput.value = "";

});

</script>


</body>

</html>