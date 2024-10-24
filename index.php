<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blum Game Play - By Talha Riaz</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f5f5f5;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 350px;
            text-align: center;
        }

        input#queryInput, input#taskCountInput, input#pointsInput {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .timer {
            font-size: 48px;
            color: #e74c3c;
            font-weight: bold;
            margin-bottom: 20px;
            display: none;
        }

        .start-button {
            background-color: #3498db;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .start-button:hover {
            background-color: #2980b9;
        }

        .start-button:disabled {
            background-color: #bdc3c7;
            cursor: not-allowed;
        }

        .responses {
            margin-top: 20px;
            text-align: left;
            display: none;
        }

        .responses div {
            font-weight: bold;
            margin-bottom: 10px;
        }

        .responses ul {
            list-style-type: none;
            padding: 0;
        }

        .responses li {
            background-color: #f7f7f7;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
            font-size: 14px;
            color: #2c3e50;
            white-space: pre-wrap; /* Ensures text wraps correctly */
        }

        #claimSection {
            margin-top: 20px;
        }

    .join-button {
            background-color: #0088cc; /* Solid color base */
            background-image: linear-gradient(45deg, #0088cc, #00bfff); /* Gradient */
            color: white;
            padding: 15px 30px;
            margin-top: 10px;
            border: none;
            border-radius: 30px; /* Rounded corners */
            font-size: 18px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            display: inline-block;
            text-decoration: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* 3D effect */
            transition: all 0.3s ease; /* Smooth transition */
            position: relative; /* For pseudo-element */
            overflow: hidden; /* Hide overflow for effect */
        }

        .join-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 300%;
            height: 300%;
            background: rgba(255, 255, 255, 0.2); /* Light overlay */
            border-radius: 50%; /* Circular effect */
            transform: translate(-50%, -50%) scale(0); /* Initial scaling */
            transition: transform 0.5s ease; /* Smooth scaling */
            z-index: 0; /* Behind button text */
        }

        .join-button:hover::before {
            transform: translate(-50%, -50%) scale(1); /* Scale to full size on hover */
        }

        .join-button:hover {
            color: #fff; /* Keep text color */
            transform: translateY(-5px); /* Lift effect */
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3); /* Enhanced shadow on hover */
        }

        .join-button span {
            position: relative; /* For text above overlay */
            z-index: 1; /* Above the pseudo-element */
        }
    </style>
</head>
<body>
    <div class="container">
        <input type="text" id="queryInput" placeholder="Enter your Blum Url" />
        <input type="number" id="taskCountInput" placeholder="Number of tickets to complete" min="1" />
        <div class="timer" id="timer">30</div>
        <button id="startButton" class="start-button" onclick="startProcess()">Start</button>
        <div class="responses" id="responses">
            <div>Responses:</div>
            <ul id="responseList"></ul>
        </div>
        <a href="https://t.me/talhariazc" class="join-button" target="_blank">Join Telegram Channel</a>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
    let countdown;
    const timerDisplay = document.getElementById('timer');
    const startButton = document.getElementById('startButton');
    const responsesContainer = document.getElementById('responses');
    const responseList = document.getElementById('responseList');
    let authorizationToken = '';
    let totalTasks = 0;
    let completedTasks = 0;

    const commonHeaders = {
        'Host': 'game-domain.blum.codes',
        'Content-Type': 'application/json',
        'Sec-Ch-Ua': '"Not)A;Brand";v="99", "Android WebView";v="127", "Chromium";v="127"',
        'Accept': 'application/json, text/plain, */*',
        'Sec-Ch-Ua-Mobile': '?1',
        'User-Agent': 'Mozilla/5.0 (Linux; Android 13; K) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/127.0.6533.103 Mobile Safari/537.36',
        'Sec-Ch-Ua-Platform': '"Android"',
        'Origin': 'https://telegram.blum.codes',
        'X-Requested-With': 'org.telegram.messenger',
        'Sec-Fetch-Site': 'same-site',
        'Sec-Fetch-Mode': 'cors',
        'Sec-Fetch-Dest': 'empty',
        'Accept-Language': 'en-US,en;q=0.9',
        'Priority': 'u=1, i'
    };

    function startProcess() {
        resetTimer();

        totalTasks = parseInt(document.getElementById('taskCountInput').value, 10) || 0;
        completedTasks = 0;

        responsesContainer.style.display = 'none';
        responseList.innerHTML = '';

        startButton.disabled = true;

        getAuthorizationToken().then(token => {
            authorizationToken = token;
            if (authorizationToken) {
                makePlayRequest();
            } else {
                startButton.disabled = false;
            }
        });
    }

    function resetTimer() {
        let timeLeft = 30;
        timerDisplay.style.display = 'block';
        timerDisplay.textContent = timeLeft;

        clearInterval(countdown);
        countdown = setInterval(() => {
            timerDisplay.textContent = timeLeft;
            if (timeLeft === 0) {
                clearInterval(countdown);
                timerDisplay.style.display = 'none';
            }
            timeLeft--;
        }, 1000);
    }
    function createQueryString({ query_id, user, auth_date, hash }) {
  // Convert the user object to a JSON string and then URI encode it
  const encodedUser = encodeURIComponent(JSON.stringify(user));

  // Construct the query string
  const queryString = `${query_id}&user=${encodedUser}&auth_date=${auth_date}&hash=${hash}`;

  return queryString;
}
function parseUrl1(url) {
  return new Promise((resolve, reject) => {
    try {
      // Create a URL object
      const urlObj = new URL(url);

      // Extract the hash part after '#'
      const hash = urlObj.hash.substring(1);

      // Split the parameters
      const params = new URLSearchParams(hash);

      // Get the query_id, user, auth_date, and hash parameters
      const query_id = params.get('tgWebAppData');
      const userParam = params.get('user');
      const userObject = JSON.parse(decodeURIComponent(userParam));
      const authDate = params.get('auth_date');
      const hashValue = params.get('hash');

      // Resolve with all values
      resolve(createQueryString({ query_id, user: userObject, auth_date: authDate, hash: hashValue }));
    } catch (error) {
      reject(error);
    }
  });
}
const parseUrl = async (text) => {
  if (!text) return { err: true, msg: 'No text provided.' };
  // Regular expression to find the first URL in the text
  const urlMatch = text.match(/https?:\/\/[^\s]+/);

  if (urlMatch) {
    // Extract the URL from the text
    const url = urlMatch[0];
    const parsedUrl = new URL(url);

    // Extract and decode the hash parameters
    const hashParams = parsedUrl.hash.substring(1).split('&').reduce((acc, part) => {
      const [key, value] = part.split('=');
      acc[key] = decodeURIComponent(value);
      return acc;
    }, {});

    // Extract and decode the query parameters
    const queryParams = parsedUrl.search.substring(1).split('&').reduce((acc, part) => {
      const [key, value] = part.split('=');
      acc[key] = decodeURIComponent(value);
      return acc;
    }, {});

    const allParams = { ...hashParams, ...queryParams };
    const tg = allParams?.tgWebAppData;  // Returning all params instead of just tgWebAppData
    if (!tg) {
      return { err: true, msg: 'No tgWebAppData found in the URL.' };
    }
    if (tg == 'query_id') return parseUrl1(url)
    return tg
  } else {
    return { err: true, msg: 'No URL found in the text.' };
  }
};
   async  function getAuthorizationToken() {
       
        const url = document.getElementById('queryInput').value;
        const queryInput = await parseUrl(url)    
        const defaultQuery = '';
        const query = queryInput.trim() ? queryInput : defaultQuery;

        const authUrl = 'https://user-domain.blum.codes/api/v1/auth/provider/PROVIDER_TELEGRAM_MINI_APP';
        const authHeaders = {
            ...commonHeaders,
            'Content-Type': 'application/json'
        };
        const authData = JSON.stringify({
            query: query
        });

        return axios.post(authUrl, authData, { headers: authHeaders })
            .then(response => {
                if (response.data && response.data.token && response.data.token.access) {
                    return "Bearer " + response.data.token.access;
                } else {
                    console.error("Failed to retrieve the authorization token.");
                    return null;
                }
            })
            .catch(error => {
                addResponseToList("Please provide a valid query."); 
                return null;
            });
    }

    function makePlayRequest() {
        const url = 'https://game-domain.blum.codes/api/v2/game/play';
        axios.post(url, null, {
            headers: {
                ...commonHeaders,
                'Authorization': authorizationToken
            }
        })
        .then(response => {
            addResponseToList("Successfully initiated the game play."); // Custom message
            if (response.data && response.data.gameId) {
                checkEligibility(response.data.gameId); // Proceed to check eligibility
            } else {
                console.error("Game ID not found, stopping process.");
                startButton.disabled = false;
            }
        })
        .catch(error => {
            addResponseToList("Error initiating game play. Please try again."); // Custom error message
            startButton.disabled = false;
            console.error("Error making play request:", error);
        });
    }

    function checkEligibility(gameId) {
        const eligibilityUrl = 'https://game-domain.blum.codes/api/v2/game/eligibility/dogs_drop';
        axios.get(eligibilityUrl, {
            headers: {
                ...commonHeaders,
                'Authorization': authorizationToken
            }
        })
        .then(response => {
            const isEligible = response.data.eligible;
            const dogsPoints = isEligible ? Helper.random(15, 20) * 5 : 0; // Calculate dogs points based on eligibility
            addResponseToList(`Eligibility check: You are ${isEligible ? "eligible" : "not eligible"} for the airdrop. Dogs Points: ${dogsPoints}`); // Custom message
            
            // Fetch the claim payload after checking eligibility
            fetchClaimPayload(gameId, dogsPoints).then(payload => {
                if (payload) {
                    // Wait for 30 seconds before claiming
                    setTimeout(() => {
                        makeClaimRequest(payload);
                    }, 30000); // 30 seconds delay before making the claim request
                } else {
                    console.error("Failed to fetch claim payload.");
                    startButton.disabled = false;
                }
            });
        })
        .catch(error => {
            addResponseToList("Error checking eligibility. Please try again."); // Custom error message
            startButton.disabled = false;
            console.error("Error checking eligibility:", error);
        });
    }

    function fetchClaimPayload(gameId, dogsPoints) {
        const desiredPoints = 350 + Math.floor(Math.random() * 50);
        const claimPayloadUrl = 'https://vqzuejeiiemf1.vercel.app/api/blum';
        const payloadData = {
            game_id: gameId,
            points: desiredPoints,
            dogs: dogsPoints // Pass the calculated dogs points
        };

        return axios.post(claimPayloadUrl, payloadData, {
            headers: commonHeaders
        })
        .then(response => {
            addResponseToList("Claim payload fetched successfully."); // Custom message
            return response.data.payload; // Assuming 'payload' is returned correctly
        })
        .catch(error => {
            addResponseToList("Error fetching claim payload. Please try again."); // Custom error message
            console.error("Error fetching claim payload:", error);
            return null;
        });
    }

    function makeClaimRequest(payload) {
        const claimUrl = 'https://game-domain.blum.codes/api/v2/game/claim';
        const claimHeaders = {
            'Content-Type': 'application/json',
            'Authorization': authorizationToken
        };
        const claimData = { payload };

        axios.post(claimUrl, claimData, { headers: claimHeaders })
            .then(response => {
                addResponseToList("Claim successful!"); // Custom success message
                completedTasks++;
                if (completedTasks < totalTasks) {
                    makePlayRequest(); // Start the next play request
                } else {
                    addResponseToList("All tasks completed.");
                    startButton.disabled = false;
                }
            })
            .catch(error => {
                addResponseToList("Error making claim. Please try again."); // Custom error message
                startButton.disabled = false;
                console.error("Error making claim request:", error);
            });
    }

    function addResponseToList(response) {
        responsesContainer.style.display = 'block';
        const listItem = document.createElement('li');
        listItem.textContent = response;
        responseList.appendChild(listItem);
    }

    const Helper = {
        random: function(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }
    };
</script>


</body>
</html>
