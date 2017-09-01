--
-- Structure de la table `transpo_topics`
--

CREATE TABLE `transpo_topics` (
  `forum_id` varchar(11) NOT NULL,
  `id` varchar(11) NOT NULL,
  `oldid` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Structure de la table `transpo_users`
--

CREATE TABLE `transpo_users` (
  `wefrag_id` varchar(11) NOT NULL,
  `id` varchar(11) NOT NULL,
  `password` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Index pour la table `transpo_users`
--
ALTER TABLE `transpo_users`
  ADD PRIMARY KEY (`wefrag_id`,`id`);
