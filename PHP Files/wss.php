<?php	//	Immeasurable Productions Musicals (IPTheater.com)
require_once '_inc/ipt_1.php';



// REMOVE THIS AFTER GREASE
$REALcurrentShow = $currentShow;
$currentShow = $nextShow;
// /REMOVE



if ($currentShow->getAbbrLower() !== 'wss') {
	header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $currentShow->getAbbrLower());
}

$title = $currentShow->getTitle() . ' Information Page';
$body_class .= ' show_info';



// REMOVE THIS AFTER GREASE
$currentShow = $REALcurrentShow;
// /REMOVE



require_once '_inc/ipt_2.php';



// REMOVE THIS AFTER GREASE
$currentShow = $nextShow;
// /REMOVE



$performancesHTML = "
	<ul class='jm list-group'>";
foreach($currentShow->getPerformances() as $p) {
	$performancesHTML .= "
		<li class='list-group-item'>{$p->getDateTimeFormatted()}</li>";
}
$performancesHTML .= "
	</ul>";

?>
<?=$header;?>
	<section class='main'>
		<div class='row'>
			<div class='col-xs-12'>
				<h2>
					<img src='/_img/ip-logo-long_400_trans.png' alt='Immeasurable Productions' class='img-responsive inline-block'>
					<br>Presents...<br>
					<img src='_img/<?=$currentShow->getAbbr();?>-logo_400.png' alt='<?=$currentShow->getTitle();?>' class='img-responsive inline-block top-buffer'>
				</h2>
			</div>
		</div>
		<div class='row'>
			<div class='col-sm-10 col-sm-offset-1'>
				<!-- <h4 id='<?=$currentShow->getAbbr();?>Link'><img src='/downloads/<?=$currentShow->getAbbr();?>%20Poster.jpg' alt='<?=$currentShow->getTitle();?>' id='<?=$currentShow->getAbbr();?>_logo'></h4> -->
				<!-- <p>We are very excited about all of the talented performers who came out to audition for us!  This show will be fantastic!  We will post the cast list right here by the end of the day today.</p> -->
				<!-- <p>We are pleased to announce the cast list for <?=$currentShow->getTitle();?>!</p> -->
				<h3>AUDITIONS &amp; CALLBACKS...</h3>
				<p>Auditions will be sometime around early November, <?=date('Y');?>.  Check back later for more information.</p>
				<!-- <p>Please fill out the <a class='btn btn-success' href='audition_form.php' title='Fill out the Audition Form!'><i class='fa fa-lg fa-pencil-square-o'></i> Audition Form</a> online in advance.</p>
				<p><strong>WHEN:</strong></p>
				<ul>
					<li>AUDITIONS: <?=date('l, F jS, g:ia', strtotime($auditionsStart));?> - <?=date('g:ia', strtotime($auditionsEnd));?>
						<ul class='small'>
							<li>First-come, first-served.</li>
							<li>Plan to be at auditions for about <?=$approxAuditionTime;?> minutes.</li>
							<li>Yes, this is on Halloween; and yes, you're certainly welcome to wear a costume!</li>
						</ul>
					</li>
					<li>ALTERNATIVELY... mark on the <a href='audition_form.php'>Audition Form</a> if you wish to send an alternate audition over YouTube.</li>
					<li>CALLBACKS: <?=date('l, F jS, g:ia', strtotime($callbacksStart));?> - <?=date('g:ia', strtotime($callbacksEnd));?> (by invitation only)</li>
				</ul>
				<p><strong>WHERE:</strong><br>
					New City Church<br>
					7456 Nieman Rd<br>
					Shawnee, KS 66203</p> -->
				<!-- <p><strong>WHO:</strong> Anyone age 10 or older may audition, but we're especially looking for teenagers and young adults.</p> -->
				<!-- <p><strong>WHO:</strong>
					<ul>
						<li>Anyone age 13 or older may audition for <em><b>High School Musical</b></em></li>
						<li>Anyone age 8 to 13 may audition for the <em><b>Wildcat Crew</b></em> ensemble (see below for details)</li>
					</ul>
				</p>
				<p><strong>WHAT TO PREPARE:</strong></p>
				<ul>
					<li>Prepare a solo audition song (approx. 30-45 seconds) with background music on CD or mp3 player.</li>
					<li>Bring comfortable shoes and clothes to dance in.</li>
					<li>For those who are unfamiliar with the roles, we encourage you to <a href='downloads/<?=$currentShow->getAbbr();?>%20Character%20Descriptions.pdf' target='_blank'>read the character descriptions</a>.</li>
					<li><strong>OPTIONAL:</strong> Download the <a class='btn btn-info' href='downloads/<?=$currentShow->getAbbr();?>%20Callback%20Package.zip' title='Download the <?=$currentShow->getTitle();?> Callback Package!' target='_blank'><i class='fa fa-lg fa-arrow-circle-o-down'></i> <?=$currentShow->getTitle();?> Callback Package</a> to prepare for callbacks.</li>
				</ul><br> -->
				<h3>PERFORMANCES...</h3>
				<p><strong>WHEN:</strong></p>
				<div class='row'>
					<div class='col-sm-8 col-md-6 col-lg-4'>
						<?=$performancesHTML;?>
					</div>
				</div>
				<p><strong>WHERE:</strong> <?=$currentShow->getTheater()->getName();?> (<?=$currentShow->getTheater()->getAddressFormatted();?>)</p>
				<!-- <p><strong>TICKETS:</strong> All tickets are $12 (reserved seating) in advance or at the door.</p>
				<h4><a href='tickets.php' class='btn btn-success'><i class='fa fa-ticket fa-lg'></i> Order Your Tickets Now</a></h4>
				<hr>
				<h2>...Cast Info...</h2> -->
				<!-- <h4><a class='btn btn-danger' href='cast_list.php' title='<?=$currentShow->getTitle();?> Cast List'><i class='fa fa-lg fa-pencil-square-o'></i> <?=$currentShow->getTitle();?> Cast List</a></h4> -->
				<!-- <h4><a class='btn btn-primary' href='cast_info.php' title='<?=$currentShow->getTitle();?> Cast Info'><i class='fa fa-lg fa-briefcase'></i> Resources for the Cast</a></h4> -->
				<!-- <h3>CAST MEETING...</h3>
				<p><strong>WHEN:</strong> <?=date('l, F jS, g:ia', strtotime($castMeetingStart));?> - <?=date('g:ia', strtotime($castMeetingEnd));?><br>
					<strong>WHERE:</strong>
					New City Church<br>
					7456 Nieman Rd<br>
					Shawnee, KS 66203<br>
					<span>(same location as auditions)</span></p> -->
				<h3>REHEARSALS...</h3>
				<p><strong>WHEN:</strong> December 26 - 31, <?=date('Y');?> from 9am to 6pm daily <span>(some selected leads or specialty dance groups will stay till 9pm)</span></p>
				<p><strong>WHERE:</strong> <?=$currentShow->getTheater()->getName();?></p>
				<!-- <h3>CAST...</h3> -->
				<!-- <p><?=$currentShow->getTitle();?> has a production fee of $<?=$productionFeeSingle;?> per cast member (or $<?=$productionFeeFamily;?> per family).  There is no tech requirement, ticket sales quota, or fund-raising.</p>
				<p>The Wildcat Crew ensemble has production fee of $100 per performer.</p> -->
				<p>NOTE: If you would like to work on a tech crew during this week (building sets, sewing costumes, working with lights and sound, etc.), <a href='contact.php'>let us know</a>, and we will do our best to get you plugged in!</p>
				<!-- <h3>NEW THIS YEAR...</h3>
				<p>We will be implementing some exciting changes this year:</p>
				<ol>
					<li><strong>SMALLER CAST SIZE AND ENSEMBLE TRACKS:</strong> By limiting our cast size to 50 people (ages 13+), we'll have individual tracks set for the entrances, exits, costume changes, and dances for each member of the ensemble!</li>
					<li><strong>WILDCAT CREW:</strong> This year, we're excited to showcase a separate cast of performers (ages 8-13) who will appear in three numbers including the full 6-minute High School Musical Megamix at the end of the show!  The Wildcat Crew finale will feature solo singers and fun dances to finish our performances off with a rush of adrenaline!  The Crew will rehearse December 26, 28 - 31, <?=date('Y');?> from 9:15am to 12:15pm daily.  These dances will be entertaining and challenging and choreographed by our very own Mindy Moritz!</li>
				</ol> -->
				<h3>ABOUT THE SHOW...</h3>
				<p>Here's a blurb about <em><?=$currentShow->getTitle();?></em>... <span>(excerpted from the licensing company's official website.)</span></p>
				<blockquote class='text-left'>From the first notes to the final breath, West Side Story is one of the most memorable musicals and greatest love stories of all time. Arthur Laurents' book remains as powerful, poignant and timely as ever. The score by Leonard Bernstein and Stephen Sondheim is widely regarded as one of the best ever written. The world's greatest love story takes to the streets in this landmark Broadway musical that is one of the theatre's finest accomplishments.<br><br>
				Shakespeare's Romeo and Juliet is transported to modern-day New York City, as two young idealistic lovers find themselves caught between warring street gangs, the "American" Jets and the Puerto Rican Sharks. Their struggle to survive in a world of hate, violence and prejudice is one of the most innovative, heart-wrenching and relevant musical dramas of our time.</blockquote>
				<h4><button class='btn btn-primary' id='showHideSynopsis'><i class='fa fa-file-text-o'></i> <span>Show</span> Detailed Plot Synopsis</button></h4>
				<blockquote class='synopsis'>
					<h4>SYNOPSIS OF <?=strtoupper($currentShow->getTitle());?></h4>
					<h5><i class='fa fa-exclamation-triangle'></i> WARNING: SPOILERS AHEAD <i class='fa fa-exclamation-triangle'></i></h5>
					<h4>Prologue</h4>
					<p>The opening is a carefully choreographed half-danced, half-mimed, ballet of sorts. It shows the growing tensions between the Sharks, a Puerto Rican gang, and the Jets, a gang made up of 'American' boys. An incident between the Jets and Shark leader Bernardo escalates into an all out fight between the two gangs. Officers Schrank and Krupke arrive to break up the fight.</p>
					<h4>Act I</h4>
					<p>Detective Schrank, the senior cop on the beat, tries to get the Jets to tell him which Puerto Ricans are starting trouble in the neighborhood, as he claims he is on their side. The Jets, however, are not stool pigeons and won't tell him anything. Frustrated, Schrank threatens to beat the crap out of the Jets unless they make nice. When the police leave, the Jets bemoan the Sharks coming onto their turf. They decide they need to have one, big rumble to settle the matter once and for all - even if winning requires fighting with knives and guns. Riff plans to have a war council with Bernardo to decide on weapons. Action wants to be his second, but Riff says that Tony is always his second. The other boys complain that Tony hasn't been around for a month. Riff doesn't care; once you're a Jet, you're a Jet for life <strong>("Jet Song")</strong>.</p>
					<p>Riff goes to see Tony who is now working at Doc's drugstore. Riff presses him to come to the school dance for the war council, but Tony resists; he's lost the thrill of being a Jet. He explains that every night for a month he's had a strange feeling that something important is just around the corner. Nevertheless, Riff convinces Tony to come to the dance. Riff leaves and Tony wonders about this strange feeling he's been having <strong>("Something's Coming")</strong>.</p>
					<p>In a bridal shop, Anita remakes Maria's communion dress into a party dress. They are both Puerto Rican. Anita is knowing, sexual and sharp. Maria is excited and enthusiastic, childlike but also growing into an adult. Maria complains that the dress is too young looking, but Anita explains that her boyfriend, Bernardo - Maria's brother - made her promise not to make the dress too short. It turns out the dress is for the dance, which Maria is going to with Chino, who she is expected to marry despite the fact that she does not have any feelings for him.</p>
					<p>At the dance in the local gym, the group is divided into Jets and their girls and Sharks and their girls. Riff and his lieutenants move to challenge Bernardo and his lieutenants, but they are interrupted by Glad Hand, the chaperone overseeing the dance and Officer Krupke. The two initiate some dances to get the kids to dance together across the gang lines. In the promenade leading up to the dance, the girls and boys end up facing each other at random, Jet girls across from Shark boys and vice versa. Bernardo reaches across the Jet girl in front of him to take Anita's hand. Riff does the same with his girlfriend Velma. Everyone dances with their own group as Tony enters <strong>("Mambo")</strong>. During the dance, Maria and Tony spot each other. There is an instant connection. Bernardo interrupts them, telling Tony to stay away from his sister and asking Chino to take her home. Riff and Bernardo agree to meet at Doc's in half an hour for the war council. As everyone else disappears, Tony is overcome with the feeling of having met the most beautiful girl ever <strong>("Maria")</strong>.</p>
					<p>Later, Tony finds the fire escape outside of Maria's apartment and calls up to her. She appears in the window, but is nervous they will get caught. Her parents call her inside, but she stays. She and Tony profess their love to each other <strong>("Tonight")</strong>. He agrees to meet her at the bridal shop the next day. Bernardo calls Maria inside. Anita admonishes him, saying that Maria also has a mother and father to take care of her. Bernardo insists that they, like Maria, don't understand this country. Bernardo, Anita, Chino and their friends discuss the unfairness of America - they are treated like foreigners, while 'Polak's' like Tony are treated like real Americans. They get paid twice as much at their jobs, too. Anita tries to lure Bernardo inside, and away from the war council, but he refuses. As the boys leave for the council, one of Anita's friends, Rosalia, claims to be homesick for Puerto Rico. Anita scoffs at this. While Rosalia expounds on the beauties of the country, Anita responds with why she prefers her new home <strong>("America")</strong>.</p>
					<p>At the drugstore, the Jets wait for the Sharks. They discuss what weapons they might have to use. Doc is upset that the boys are planning to fight at all. Anybodys, a tomboy who is trying to join the Jets, asks Riff if she can participate in the rumble, but he says no. Doc doesn't understand why the boys are making trouble for the Puerto Ricans, and the boys respond that the Sharks make trouble for them. Doc calls them hoodlums and Action and A-rab get very upset. Riff tells them they have to save their steam for the rumble and keep cool, rather than freaking out <strong>("Cool")</strong>.</p>
					<p>Bernardo arrives at the drugstore and he and Riff begin laying out the terms of the rumble. Tony arrives and convinces them all to a fair fight - just skin, no weapons. The Sharks' best man fights the Jets' best man; Bernardo agrees, thinking that means he will get to fight Tony, but the Jets say they get to pick their fighter. Schrank arrives and breaks up the council. He tells the Puerto Ricans to get out. Bernardo and his gang exit. Schrank tries to get the Jets to tell him where the rumble is, and becomes increasingly frustrated as they refuse. He insults them and leaves. As Tony and Doc close up the shop, Tony reveals he's in love with a Puerto Rican. Doc is worried.</p>
					<p>The next day at the bridal shop, Maria tells Anita she can leave and that Maria will clean up. Anita is about to go when Tony arrives. She suddenly understands and promises not to tell on them. When she leaves, Tony tells Maria the rumble will be a fair fight. That is not good enough for her, and she asks him to go to the rumble and stop it. He agrees. He'll do anything for her. They fantasize about being together and getting married <strong>("One Hand, One Heart")</strong>. Later, the members of the ensemble wait expectantly for the fight, all for different reasons <strong>("Tonight Quintet")</strong>.</p>
					<p>At the rumble, Diesel and Bernardo prepare to fight, with Chino and Riff as their seconds. Tony enters and tries to break up the fight, but instead provokes Bernardo against him. Bernardo calls Tony chicken for not fighting him. Riff punches Bernardo and the fight escalates quickly. Riff and Bernardo pull out knives, Bernardo kills Riff and in response Tony kills Bernardo, instantly horrified by what he's done. The police arrive and everyone clears out, with Anybodys getting Tony out in time.</p>
					<h4>Act II</h4>
					<p>In Maria's apartment, she gushes to her friends about how it is her wedding night and she is so excited <strong>("I Feel Pretty")</strong>. Chino interrupts her reverie. He tells her that Tony has killed Bernardo. She refuses to believe, but when Tony arrives on her fire escape he confesses. He offers to turn himself in, but she begs him to stay with her. She says that though they are together, everyone is against them. Tony says they'll find a place where they can be together <strong>("Somewhere")</strong>.</p>
					<p>In a back alley, the Jets regroup in shock. No one has seen Tony. Officer Krupke comes by, threatening to take them to the station house. The boys chase him away for the moment. The boys release some tension by play-acting the scenario of what would happen if Krupke actually did take them to the station house <strong>("Gee, Officer Krupke")</strong>. Anybodys shows up with information about Tony, and the fact that Chino is looking for him. She uses this information to get the boys to treat her like one of the gang. The Jets agree that they need to find Tony and warn him about Chino.</p>
					<p>Meanwhile, Anita comes into Maria's room and finds her with Tony. Tony and Maria are planning to runaway. Tony knows Doc will give him money, so he goes to the drugstore and tells Maria to meet him there. She agrees. When he leaves, Anita explodes at her for loving the boy who killed her brother. Maria acknowledges that it's not smart, but she can't help it <strong>("A Boy Like That/I Have A Love")</strong>. Anita tells Maria that Chino has a gun and is looking for Tony. Schrank arrives and detains Maria for questioning. Maria covertly asks Anita to go to Doc's and tell Tony she has been delayed. Reluctantly, Anita agrees.</p>
					<p>The Jets arrive at Doc's, learning that Tony and Doc are in the basement. Anita arrives and asks to speak to Doc. The Jets, recognizing her as Bernardo's girl and thinking she is here to betray Tony to Chino, won't let her go down to the basement to talk to Doc. Instead they harass and attack her. Doc arrives to find them ganging up on her; he breaks it up but Anita, disgusted and hurt, lies to Doc and tells him to relay a message to Tony: Chino has shot Maria and he will never see her again.</p>
					<p>When Doc returns to Tony in the basement, he delivers Anita's message. Tony is distraught and heart broken. He runs out into the streets and calls Chino to come for him. Anybodys tries to stop him, but Tony doesn't care. He yells to Chino that he should shoot him, too. Maria appears in the street - much to Tony's surprise - and they run towards each other. In that moment, Chino also arrives. He shoots Tony and falls into Maria's arms. He is dying.</p>
					<p>The Jets, Sharks and Doc appear on the street. Maria picks up the gun and points it all of them, asking Chino if there are enough bullets to kill all of them and herself, as well. The depths of her sadness and anger move everyone, as she breaks down over Tony's body. Officers Krupke and Schrank arrive. They and Doc watch as two boys from each gang pick up Tony's body and form a processional. The rest follow the processional, with Baby John picking up Maria's shawl, giving it to her and helping her up. As Maria follows the others, the adults watch on <strong>("Finale")</strong>.</p>
				</blockquote>
				<p>We are excited to make this another unforgettable show!  <a href='contact.php'>Contact us</a> if you wish to be added to the email list to receive the latest updates regarding auditions, rehearsals and performances.  We look forward to working with you to create what is bound to be an outstanding production of <em><?=$currentShow->getTitle();?></em>!</p>
				<img src='_img/<?=$currentShow->getAbbr();?>-stage-photo.jpg' alt='' class='img-responsive center'>
				<div class='cutout hidden-xs'>
					<img src='_img/cutouts/maggie-christian.png' alt=''>
				</div>
			</div>
		</div>
	</section>
<?=$footer;?>
